<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::with('zone')->where('status', 'available')->inRandomOrder()->take(6)->get();
        $services      = Service::all();
        $promotions    = collect();
        $reviews       = collect();
        $totalRooms    = Room::count();
        $totalServices = Service::count();
        return view('welcome', compact('featuredRooms', 'services', 'promotions', 'reviews', 'totalRooms', 'totalServices'));
    }

    public function rooms()
    {
        $rooms = Room::with('zone')->where('status', 'available')->paginate(9);
        return view('rooms', compact('rooms'));
    }

    public function showRoom($id)
    {
        $room = Room::with('zone')->findOrFail($id);
        return view('room-detail', compact('room'));
    }

    // POST /rooms/search — search bar trang welcome
    public function searchByDate(Request $request)
    {
        $request->validate([
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);
        session(['search_check_in' => $request->check_in, 'search_check_out' => $request->check_out]);
        return redirect()->route('rooms.index', ['check_in' => $request->check_in, 'check_out' => $request->check_out]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /booking/checkout
    // Nhận ngày từ form room-detail → tạo soft lock → hiện checkout
    // ─────────────────────────────────────────────────────────────
    public function checkout(Request $request)
    {
        $request->validate([
            'preferred_room_id' => 'required|exists:rooms,id',
            'check_in'          => 'required|date|after_or_equal:today',
            'check_out'         => 'required|date|after:check_in',
        ]);

        // Nếu chưa đăng nhập → lưu intent vào session rồi redirect login
        // Sau khi login, Breeze gọi redirect()->intended() → về /booking/resume
        // /booking/resume sẽ đọc session và tự xử lý tiếp
        if (!Auth::check()) {
            session([
                'booking_intent' => [
                    'room_id'   => $request->preferred_room_id,
                    'check_in'  => $request->check_in,
                    'check_out' => $request->check_out,
                ]
            ]);
            // Đặt intended URL để Breeze redirect về đây sau login
            session(['url.intended' => route('booking.resume')]);
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để tiếp tục đặt phòng.');
        }

        return $this->processCheckout(
            $request->preferred_room_id,
            $request->check_in,
            $request->check_out
        );
    }

    // ─────────────────────────────────────────────────────────────
    // GET /booking/resume  (chỉ vào được khi đã đăng nhập)
    // Breeze redirect về đây sau khi login thành công
    // Đọc booking_intent từ session và tiếp tục luồng checkout
    // ─────────────────────────────────────────────────────────────
    public function resumeAfterLogin()
    {
        $intent = session('booking_intent');

        if (!$intent) {
            // Không có intent → về dashboard bình thường
            return redirect()->route('dashboard');
        }

        // Xóa intent khỏi session để không bị loop
        session()->forget('booking_intent');

        return $this->processCheckout(
            $intent['room_id'],
            $intent['check_in'],
            $intent['check_out']
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Logic xử lý checkout dùng chung cho cả 2 route trên
    // ─────────────────────────────────────────────────────────────
    private function processCheckout(string $roomId, string $checkInRaw, string $checkOutRaw)
    {
        $checkIn  = Carbon::parse($checkInRaw)->startOfDay();
        $checkOut = Carbon::parse($checkOutRaw)->startOfDay();
        $room     = Room::with('zone')->findOrFail($roomId);

        DB::beginTransaction();
        try {
            // Kiểm tra conflict (holding chưa hết hạn hoặc đã đặt)
            $conflict = Order::where('room_id', $room->id)
                ->whereNotIn('status', ['cancelled', 'checked_out'])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
                })
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return redirect()->route('rooms.show', $room->id)
                    ->with('error', 'Rất tiếc, phòng đang được giữ hoặc đã đặt trong khoảng thời gian này. Vui lòng chọn ngày khác hoặc thử lại sau ít phút.');
            }

            // Xóa holding cũ của chính user này cho phòng này (nếu quay lại đặt lại)
            Order::where('room_id', $room->id)
                ->where('user_id', Auth::id())
                ->where('status', 'holding')
                ->delete();

            // Tạo / lấy Customer
            $customer = Customer::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'full_name'       => Auth::user()->name,
                    'phone'           => Auth::user()->phone ?? '',
                    'email'           => Auth::user()->email,
                    'loyalty_tier_id' => 1,
                ]
            );

            // Tạo soft lock
            $holdOrder = Order::create([
                'customer_id'    => $customer->id,
                'room_id'        => $room->id,
                'user_id'        => Auth::id(),
                'check_in'       => $checkIn,
                'check_out'      => $checkOut,
                'total_guests'   => 1,
                'total_price'    => 0,
                'deposit_amount' => 0,
                'payment_status' => 'unpaid',
                'status'         => 'holding',
                'transfer_code'  => 'HOLD' . strtoupper(Str::random(6)),
                'expires_at'     => now()->addMinutes(10),
            ]);

            DB::commit();

            $services = Service::all();
            $nights   = $checkIn->diffInDays($checkOut);

            return view('booking.checkout', compact(
                'room', 'checkIn', 'checkOut', 'services', 'nights', 'holdOrder'
            ));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout soft lock error: ' . $e->getMessage());
            return redirect()->route('rooms.show', $room->id)
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }
}