<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Bill;
use App\Mail\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MomoController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Tạo payment request gửi lên MoMo sandbox
    // ─────────────────────────────────────────────────────────────
    public function createPayment(Order $order, Bill $bill)
    {
        $partnerCode = config('payment.momo_partner_code', 'MOMO');
        $accessKey   = config('payment.momo_access_key',   'F8BBA842ECF85');
        $secretKey   = config('payment.momo_secret_key',   'K951B6PE1waDMi640xX08PD3vg6EkVlz');
        $endpoint    = config('payment.momo_endpoint',     'https://test-payment.momo.vn/v2/gateway/api/create');

        // Thêm timestamp để tránh trùng orderId khi retry
        $orderId     = $order->transfer_code . '_' . time();
        $orderInfo   = 'Dat coc phong ' . ($order->room->room_number ?? '') . ' - Resort Pro';
        $amount      = (string) $order->deposit_amount;
        $redirectUrl = route('booking.payment', $order);
        $ipnUrl      = config('payment.momo_ipn_url', '');
        $requestId   = $partnerCode . time();
        $requestType = 'captureWallet'; // captureWallet để có QR MoMo
        $extraData   = '';

        $rawHash = "accessKey={$accessKey}"
                 . "&amount={$amount}"
                 . "&extraData={$extraData}"
                 . "&ipnUrl={$ipnUrl}"
                 . "&orderId={$orderId}"
                 . "&orderInfo={$orderInfo}"
                 . "&partnerCode={$partnerCode}"
                 . "&redirectUrl={$redirectUrl}"
                 . "&requestId={$requestId}"
                 . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || !$result || $httpCode !== 200) {
            Log::error('MoMo API curl error', ['code' => $httpCode, 'error' => $curlErr]);
            return null;
        }

        $response = json_decode($result, true);
        Log::info('MoMo API response', $response);

        if (($response['resultCode'] ?? -1) !== 0) {
            Log::error('MoMo payment failed', $response);
            return null;
        }

        // qrCodeUrl của MoMo là deeplink (momo://...)
        // Tạo QR image từ deeplink đó bằng Google Charts
        $momoDeeplink = $response['qrCodeUrl'] ?? null;
        $momoQrImage  = null;
        if ($momoDeeplink) {
            // Dùng api.qrserver.com thay chart.googleapis.com (ít bị chặn hơn)
            $momoQrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='
                         . urlencode($momoDeeplink);
        }

        $updateData = [
            'bank_payload' => [
                'momo_request_id' => $requestId,
                'momo_order_id'   => $orderId,
                'momo_pay_url'    => $response['payUrl'] ?? null,
                'momo_deeplink'   => $momoDeeplink,
                'momo_qr_image'   => $momoQrImage,
            ],
        ];

        // Chỉ override qr_image_url nếu có QR thật từ MoMo
        if ($momoQrImage) {
            $updateData['qr_image_url'] = $momoQrImage;
        }

        $bill->update($updateData);
        Log::info('MoMo bill updated', ['bill_id' => $bill->id, 'qr' => $momoQrImage]);

        return $response;
    }

    // ─────────────────────────────────────────────────────────────
    // IPN Webhook — MoMo gọi về khi thanh toán xong
    // POST /momo/ipn
    // ─────────────────────────────────────────────────────────────
    public function ipn(Request $request)
    {
        Log::info('MoMo IPN received', $request->all());

        $secretKey = config('payment.momo_secret_key', 'K951B6PE1waDMi640xX08PD3vg6EkVlz');
        $data      = $request->all();

        // Xác minh chữ ký
        $rawHash = "accessKey="    . ($data['accessKey']    ?? '')
                 . "&amount="      . ($data['amount']       ?? '')
                 . "&extraData="   . ($data['extraData']    ?? '')
                 . "&message="     . ($data['message']      ?? '')
                 . "&orderId="     . ($data['orderId']      ?? '')
                 . "&orderInfo="   . ($data['orderInfo']    ?? '')
                 . "&orderType="   . ($data['orderType']    ?? '')
                 . "&partnerCode=" . ($data['partnerCode']  ?? '')
                 . "&payType="     . ($data['payType']      ?? '')
                 . "&requestId="   . ($data['requestId']    ?? '')
                 . "&responseTime=". ($data['responseTime'] ?? '')
                 . "&resultCode="  . ($data['resultCode']   ?? '')
                 . "&transId="     . ($data['transId']      ?? '');

        $expected = hash_hmac('sha256', $rawHash, $secretKey);

        if ($expected !== ($data['signature'] ?? '')) {
            Log::warning('MoMo IPN invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if (($data['resultCode'] ?? -1) !== 0) {
            Log::info('MoMo IPN payment not successful', ['resultCode' => $data['resultCode']]);
            return response()->json(['message' => 'Not successful']);
        }

        // orderId có dạng RPXXXXXXXX_timestamp, lấy phần transfer_code gốc
        $transferCode = explode('_', $data['orderId'] ?? '')[0];

        $order = Order::where('transfer_code', $transferCode)
            ->where('status', 'pending')
            ->with(['customer', 'bill', 'room.zone'])
            ->first();

        if (!$order) {
            Log::warning('MoMo IPN order not found', ['transferCode' => $transferCode]);
            return response()->json(['message' => 'Order not found']);
        }

        // Chống replay attack
        $existingPayload = $order->bill?->bank_payload ?? [];
        if (($existingPayload['momo_trans_id'] ?? null) === (string)($data['transId'] ?? '')) {
            return response()->json(['message' => 'Already processed']);
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status'         => 'confirmed',
                'payment_status' => 'paid',
            ]);

            $order->bill()->update([
                'confirm_status' => 'confirmed',
                'confirmed_at'   => now(),
                'payment_date'   => now(),
                'bank_payload'   => array_merge($existingPayload, [
                    'momo_trans_id'    => (string)($data['transId'] ?? ''),
                    'momo_result_code' => $data['resultCode'],
                ]),
            ]);

            $order->customer()->increment('total_spent', $order->deposit_amount);

            if ($order->customer?->email) {
                Mail::to($order->customer->email)
                    ->queue(new BookingConfirmation($order));
            }

            DB::commit();
            Log::info('MoMo IPN: confirmed order ' . $order->id);
            return response()->json(['message' => 'Success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MoMo IPN error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }
}