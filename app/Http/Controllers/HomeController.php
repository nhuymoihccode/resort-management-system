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

    // ─────────────────────────────────────────────────────────────
    // GET /rooms — danh sách phòng với filter & availability
    // ─────────────────────────────────────────────────────────────
    public function rooms(Request $request)
    {
        // ✨ VALIDATE: chỉ validate filter params (date đã lưu session, không nhận từ URL)
        $request->validate([
            'type'      => 'nullable|in:standard,suite,villa,bungalow',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'capacity'  => 'nullable|numeric|min:1',
        ]);

        // Ngày luôn đọc từ session — không cho phép truyền qua URL
        // Lý do: tránh lộ thông tin và tránh user giả mạo ngày qua URL
        $checkIn  = session('search_check_in');
        $checkOut = session('search_check_out');
        
        // Filter từ form filter bar
        $type     = $request->input('type');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $capacity = $request->input('capacity');

        // Build query
        $query = Room::with('zone')->where('status', 'available');

        // Filter theo loại
        if ($type && in_array($type, ['standard', 'suite', 'villa', 'bungalow'])) {
            $query->where('type', $type);
        }

        // Filter theo giá
        if ($minPrice) {
            $query->where('price', '>=', intval($minPrice));
        }
        if ($maxPrice) {
            $query->where('price', '<=', intval($maxPrice));
        }

        // Filter theo sức chứa
        if ($capacity) {
            $query->where('capacity_adults', '>=', intval($capacity));
        }

        // ══ AVAILABILITY CHECK (OPTIMIZED) ══
        // Nếu có check_in/out, chỉ hiển thị phòng còn trống trong khoảng đó
        // 🚀 FIX: Dùng subquery thay vì pluck()->unique() (tránh load 10k+ rows vào memory)
        if ($checkIn && $checkOut) {
            $checkInDate  = Carbon::parse($checkIn)->startOfDay();
            $checkOutDate = Carbon::parse($checkOut)->startOfDay();

            // Subquery: Lấy room_id bị conflict mà không load toàn bộ data
            $unavailableSubquery = Order::select('room_id')
                ->where(function ($q) use ($checkInDate, $checkOutDate) {
                    $q->whereNotIn('status', ['cancelled', 'checked_out'])
                      ->where('check_in', '<', $checkOutDate)
                      ->where('check_out', '>', $checkInDate)
                      ->where(function ($subQ) {
                          $subQ->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                      });
                })
                ->distinct();

            // Loại bỏ những phòng bị conflict khỏi query (subquery hiệu quả hơn)
            $query->whereNotIn('id', $unavailableSubquery);
        }

        // ✨ FIX: withQueryString() giữ lại các tham số filter khi chuyển trang
        // ⚠️ IMPORTANT: withQueryString() phải SAU paginate(), không SAI vào Builder
        $rooms = $query->paginate(9)->withQueryString();

        // Gửi về view để hiển thị filter bar
        return view('rooms', compact(
            'rooms', 
            'checkIn', 
            'checkOut', 
            'type', 
            'minPrice', 
            'maxPrice', 
            'capacity'
        ));
    }

    /**
     * Route Model Binding: Laravel tự tìm Room theo slug nhờ getRouteKeyName().
     * Tên tham số $room phải khớp với {room} trong web.php.
     */
    public function showRoom(Room $room)
    {
        $room->loadMissing('zone');

        $checkIn  = session('search_check_in');
        $checkOut = session('search_check_out');

        return view('room-detail', compact('room', 'checkIn', 'checkOut'));
    }

    // POST /rooms/search — search bar trang welcome
    public function searchByDate(Request $request)
    {
        $request->validate([
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);
        
        // Lưu vào session — KHÔNG đưa lên URL để tránh lộ ngày
        session([
            'search_check_in'  => $request->check_in,
            'search_check_out' => $request->check_out,
        ]);

        // Redirect sạch, không kèm params
        return redirect()->route('rooms.index');
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
                return redirect()->route('rooms.show', $room->slug)
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
            return redirect()->route('rooms.show', $room->slug)
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }
}