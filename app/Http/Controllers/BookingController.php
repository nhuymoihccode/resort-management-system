<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Room;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Bill;
use App\Mail\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    // CHUYỂN SOFT LOCK -> ĐƠN HÀNG THẬT
    public function store(Request $request)
    {
        $request->validate([
            'hold_order_id' => 'required|exists:orders,id',
            'total_guests' => 'required|integer|min:1|max:10',
            'services' => 'nullable|array',
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // 1. Khóa và lấy đơn Holding
            $holdOrder = Order::where('id', $request->hold_order_id)
                ->where('user_id', Auth::id())
                ->where('status', 'holding')
                ->where('expires_at', '>', now()) // Phải còn hạn giữ phòng
                ->lockForUpdate()
                ->first();

            if (!$holdOrder) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Phiên giữ phòng đã hết hạn. Vui lòng quay lại chọn phòng.',
                    'expired' => true,
                ], 409);
            }

            $room = Room::findOrFail($holdOrder->room_id);
            $nights = Carbon::parse($holdOrder->check_in)->diffInDays(Carbon::parse($holdOrder->check_out));

            // 2. Tính toán tiền bạc
            $roomTotal = $room->price * $nights;
            $serviceTotal = 0;
            $serviceItems = [];

            if ($request->services) {
                $services = Service::whereIn('id', collect($request->services)->pluck('id'))->get()->keyBy('id');
                foreach ($request->services as $item) {
                    $svc = $services[$item['id']] ?? null;
                    if (!$svc)
                        continue;
                    $qty = (int) ($item['qty'] ?? 1);
                    $serviceTotal += $svc->price * $qty;
                    $serviceItems[] = ['service' => $svc, 'qty' => $qty];
                }
            }

            $totalPrice = $roomTotal + $serviceTotal;
            $depositAmount = (int) ceil($totalPrice * config('payment.deposit_rate', 0.3));
            $transferCode = 'RP' . strtoupper(Str::random(8));

            // 3. Cập nhật Đơn hàng (QUAN TRỌNG: RESET LẠI THỜI GIAN 15 PHÚT ĐỂ THANH TOÁN)
            $holdOrder->update([
                'status' => 'pending', // Trạng thái chờ thanh toán
                'total_guests' => $request->total_guests,
                'total_price' => $totalPrice,
                'deposit_amount' => $depositAmount,
                'transfer_code' => $transferCode,
                'note' => $request->note,
                'expires_at' => now()->addMinutes(config('payment.hold_minutes', 15)), // Reset 15p mới!
            ]);

            // 4. Lưu Services
            foreach ($serviceItems as $item) {
                DB::table('order_service')->insert([
                    'order_id' => $holdOrder->id,
                    'service_id' => $item['service']->id,
                    'quantity' => $item['qty'],
                    'price_at_time' => $item['service']->price,
                    'created_at' => now(),
                ]);
            }

            // 5. Tạo Bill & Link VietQR tĩnh
            $bankCode = config('payment.bank_code', 'TCB');
            $accountNo = config('payment.account_no', '');
            $accountName = config('payment.account_name', 'RESORT PRO');
            $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact2.png?amount={$depositAmount}&addInfo={$transferCode}&accountName=" . urlencode($accountName);

            $bill = Bill::create([
                'order_id' => $holdOrder->id,
                'transaction_id' => $transferCode,
                'total_amount' => $depositAmount,
                'qr_image_url' => $qrUrl,  // VietQR fallback
                'confirm_status' => 'pending',
            ]);

            DB::commit();

            // Gọi MoMo SAU khi commit để không rollback bill nếu MoMo lỗi
            try {
                $bill->refresh(); // Lấy lại bill từ DB
                $momoCtrl = new \App\Http\Controllers\MomoController();
                $momoCtrl->createPayment($holdOrder, $bill);
            } catch (\Exception $e) {
                \Log::warning('MoMo payment creation failed, falling back to VietQR: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'redirect_url' => route('booking.payment', $holdOrder),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function showPayment(Order $order)
    {
        if ($order->user_id !== Auth::id())
            abort(403);

        if ($order->expires_at && now()->gt($order->expires_at) && $order->status === 'pending') {
            $order->update(['status' => 'cancelled', 'canceled_at' => now()]);
            return redirect()->route('rooms.index')->with('error', 'Đơn đặt phòng đã hết hạn thanh toán.');
        }

        $order->load(['room.zone', 'bill']);
        $services = DB::table('order_service')
            ->join('services', 'services.id', '=', 'order_service.service_id')
            ->where('order_service.order_id', $order->id)
            ->select('services.name', 'order_service.quantity', 'order_service.price_at_time')->get();

        return view('booking.payment', compact('order', 'services'));
    }

    public function pollStatus(Order $order)
    {
        return response()->json([
            'status' => $order->status,
            'confirm_status' => $order->bill?->confirm_status ?? 'pending',
        ]);
    }

    // Nút Admin tự xác nhận bằng tay (Do đã bỏ Webhook tự động)
    public function confirmPayment(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->update(['status' => 'confirmed', 'payment_status' => 'paid']);
            $order->bill()->update(['confirm_status' => 'confirmed', 'confirmed_at' => now(), 'confirmed_by' => Auth::id()]);
            $order->customer()->increment('total_spent', $order->deposit_amount);
            $order->customer->fresh()->checkAndUpgradeTier(); // ← thêm dòng này

            // Gửi email
            $order->load(['room.zone', 'customer', 'user']);
            if ($order->user?->email) {
                Mail::to($order->user->email)->queue(new BookingConfirmation($order));
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Hủy holding khi khách bấm "Đổi ngày"
    // GET /booking/{order}/cancel-holding
    // ─────────────────────────────────────────────────────────────
    public function cancelHolding(Order $order)
    {
        if ($order->user_id !== Auth::id())
            abort(403);

        // Load slug trước khi xóa (sau delete mất relation)
        $roomSlug = $order->room->slug;

        // Chỉ xóa nếu vẫn còn trạng thái holding
        if ($order->status === 'holding') {
            $order->delete();
        }

        // Redirect về room_detail — dùng slug, không dùng id
        return redirect()->route('rooms.show', $roomSlug)
            ->with('info', 'Vui lòng chọn ngày mới.');
    }

    // ─────────────────────────────────────────────────────────────
    // Khách hủy đơn pending
    // POST /booking/{order}/cancel
    // ─────────────────────────────────────────────────────────────
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id())
            abort(403);
        if (!in_array($order->status, ['holding', 'pending'])) {
            return response()->json(['success' => false, 'message' => 'Không thể hủy đơn này.'], 422);
        }
        $order->update(['status' => 'cancelled', 'canceled_at' => now()]);
        $order->bill()?->update(['confirm_status' => 'failed']);
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // Làm mới QR MoMo khi hết hạn
    // POST /booking/{order}/refresh-qr
    // ─────────────────────────────────────────────────────────────
    public function refreshQr(Order $order)
    {
        if ($order->user_id !== Auth::id())
            abort(403);

        $bill = $order->bill;
        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Bill không tồn tại.']);
        }

        try {
            $momoCtrl = new \App\Http\Controllers\MomoController();
            $result = $momoCtrl->createPayment($order, $bill);

            if (!$result) {
                return response()->json(['success' => false, 'message' => 'Không thể tạo QR mới.']);
            }

            $bill->refresh();
            return response()->json([
                'success' => true,
                'qr_url' => $bill->qr_image_url,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}