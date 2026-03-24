<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SepayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Xác thực API Key
        $apiKey = str_replace('Bearer ', '', $request->header('Authorization') ?? $request->input('apiKey', ''));
        if ($apiKey !== config('payment.sepay_api_key')) {
            Log::warning('Sepay: Sai API Key');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 2. Chỉ nhận tiền VÀO
        if ($request->input('transferType') !== 'in') {
            return response()->json(['message' => 'Ignored (not incoming)']);
        }

        $content = strtoupper($request->input('content', ''));
        $amount  = (int) $request->input('transferAmount', 0);
        
        // 3. Quét mã RP trong nội dung chuyển khoản
        preg_match('/RP[A-Z0-9]{8}/', $content, $matches);
        $transferCode = $matches[0] ?? strtoupper($request->input('code', ''));

        if (!$transferCode) {
            return response()->json(['message' => 'No transfer code found']);
        }

        // 4. Tìm đơn hàng
        $order = Order::where('transfer_code', $transferCode)
                      ->where('status', 'pending')
                      ->with(['customer', 'bill'])
                      ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or already processed']);
        }

        // 5. Xác nhận đơn hàng
        DB::beginTransaction();
        try {
            // Cập nhật Orders
            $order->update(['status' => 'confirmed']);

            // Cập nhật Bills
            if ($order->bill) {
                $order->bill->update([
                    'confirm_status' => 'confirmed',
                    'confirmed_at'   => now(),
                    'payment_date'   => $request->input('transactionDate', now()),
                ]);
            }

            // Gửi Email (Dùng queue để webhook không bị timeout)
            if ($order->customer?->email) {
                // Lấy danh sách dịch vụ nếu có order_service
                $services = DB::table('order_service')
                    ->join('services', 'services.id', '=', 'order_service.service_id')
                    ->where('order_service.order_id', $order->id)
                    ->select('services.name', 'order_service.quantity', 'order_service.price_at_time')
                    ->get();

                // LƯU Ý: Đổi 'send' thành 'queue' (nếu bạn đã setup queue) hoặc giữ 'send' nếu local
                Mail::to($order->customer->email)->send(new BookingConfirmation($order, $services));
            }

            DB::commit();
            Log::info("Sepay: Đã xác nhận đơn {$order->id}");
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Sepay: Lỗi xác nhận đơn {$order->id} - " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}