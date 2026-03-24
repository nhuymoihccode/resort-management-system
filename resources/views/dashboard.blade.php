@extends('layouts.frontend')
@section('title', 'Dashboard · Resort Pro')
@section('has_hero', 'false')

@section('content')
@php
    $statusConfig = [
        'pending'     => ['label'=>'Chờ thanh toán', 'bg'=>'bg-amber-100','text'=>'text-amber-800','dot'=>'bg-amber-500','dark_bg'=>'dark:bg-amber-900/30','dark_text'=>'dark:text-amber-400'],
        'confirmed'   => ['label'=>'Đã xác nhận',   'bg'=>'bg-emerald-100','text'=>'text-emerald-800','dot'=>'bg-emerald-500','dark_bg'=>'dark:bg-emerald-900/30','dark_text'=>'dark:text-emerald-400'],
        'checked_in'  => ['label'=>'Đang lưu trú',  'bg'=>'bg-blue-100','text'=>'text-blue-800','dot'=>'bg-blue-500','dark_bg'=>'dark:bg-blue-900/30','dark_text'=>'dark:text-blue-400'],
        'checked_out' => ['label'=>'Đã trả phòng',  'bg'=>'bg-slate-100','text'=>'text-slate-600','dot'=>'bg-slate-400','dark_bg'=>'dark:bg-slate-700','dark_text'=>'dark:text-slate-400'],
        'cancelled'   => ['label'=>'Đã hủy',         'bg'=>'bg-red-100','text'=>'text-red-700','dot'=>'bg-red-400','dark_bg'=>'dark:bg-red-900/30','dark_text'=>'dark:text-red-400'],
    ];
    $tierData = [
        1 => ['name'=>'Thành viên','icon'=>'🌿','color'=>'#64748b','next'=>10000000,'next_name'=>'Bạc'],
        2 => ['name'=>'Bạc',       'icon'=>'⭐','color'=>'#3b82f6','next'=>50000000,'next_name'=>'Vàng'],
        3 => ['name'=>'Vàng',      'icon'=>'👑','color'=>'#d97706','next'=>null,    'next_name'=>null],
    ];
    $tier     = $tierData[$tierId] ?? $tierData[1];
    $spent    = $customer?->total_spent ?? 0;
    $progress = $tier['next'] ? min(100, round($spent / $tier['next'] * 100)) : 100;
    $typeLabels = ['standard'=>'Tiêu Chuẩn','suite'=>'Phòng Cao Cấp','villa'=>'Villa','bungalow'=>'Bungalow'];
@endphp

<div class="min-h-screen bg-[#f5f3ef] dark:bg-slate-900 transition-colors duration-300">

    {{-- ── HERO HEADER ── --}}
    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);">
        {{-- Decorative bg --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, #d97706 0%, transparent 50%), radial-gradient(circle at 80% 20%, #f59e0b 0%, transparent 40%)"></div>

        <div class="relative max-w-5xl mx-auto px-4 pt-24 pb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">

                {{-- User info --}}
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500 flex items-center justify-center text-white text-3xl font-black shadow-lg flex-shrink-0">
                        {{ strtoupper(mb_substr($user->name, 0, 1, 'UTF-8')) }}
                    </div>
                    <div>
                        <p class="text-amber-400 text-[10px] font-bold tracking-[.25em] uppercase mb-0.5">Tài khoản của bạn</p>
                        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white">{{ $user->name }}</h1>
                        <p class="text-slate-400 text-sm mt-0.5">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Tier card --}}
                <div class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-4 min-w-[220px]">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-3xl">{{ $tier['icon'] }}</span>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Hạng thành viên</p>
                            <p class="text-white font-bold text-lg" style="color: {{ $tier['color'] }}">{{ $tier['name'] }}</p>
                        </div>
                    </div>
                    @if($tier['next'])
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-400">{{ number_format($spent) }}đ</span>
                            <span class="text-slate-400">{{ number_format($tier['next']) }}đ</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-500" style="width:{{ $progress }}%; background:{{ $tier['color'] }}"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5">
                            Còn <strong class="text-white">{{ number_format($tier['next'] - $spent) }}đ</strong> để lên hạng <strong style="color:{{ $tier['color'] }}">{{ $tier['next_name'] }}</strong>
                        </p>
                    </div>
                    @else
                    <p class="text-amber-400 text-sm font-semibold">Hạng cao nhất 🎉</p>
                    @endif
                </div>
            </div>

            {{-- Stats inline --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
                @foreach([
                    ['Tổng đặt phòng', $orders->count(),            '📅'],
                    ['Đã xác nhận',    $confirmedCount,             '✅'],
                    ['Chờ thanh toán', $pendingCount,               '⏳'],
                    ['Tổng chi tiêu',  number_format($totalSpent).'đ','💳'],
                ] as [$label, $val, $icon])
                <div class="bg-white/10 backdrop-blur border border-white/10 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">{{ $icon }}</span>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $label }}</p>
                    </div>
                    <p class="text-xl font-bold text-white">{{ $val }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── ORDERS ── --}}
    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        <div class="flex items-center justify-between mb-2">
            <h2 class="font-serif font-bold text-slate-900 dark:text-white text-xl">Lịch sử đặt phòng</h2>
            <a href="{{ route('rooms.index') }}"
               class="text-xs font-bold text-amber-600 hover:text-amber-500 uppercase tracking-widest transition-colors">
                + Đặt phòng mới
            </a>
        </div>

        @if($orders->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="w-16 h-16 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center mb-4 text-3xl">🏨</div>
            <p class="font-semibold text-slate-700 dark:text-slate-300 mb-1">Chưa có đặt phòng nào</p>
            <p class="text-sm text-slate-400 mb-5">Khám phá các phòng và bắt đầu kỳ nghỉ!</p>
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                Xem phòng trống →
            </a>
        </div>
        @else
        @foreach($orders as $order)
        @php
            $sc     = $statusConfig[$order->status] ?? $statusConfig['pending'];
            $nights = \Carbon\Carbon::parse($order->check_in)->diffInDays(\Carbon\Carbon::parse($order->check_out));
            $svcItems = \Illuminate\Support\Facades\DB::table('order_service')
                ->join('services','services.id','=','order_service.service_id')
                ->where('order_service.order_id', $order->id)
                ->select('services.name','order_service.quantity','order_service.price_at_time')
                ->get();
        @endphp

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

            {{-- Card header --}}
            <div class="px-5 py-4 flex items-start justify-between gap-4">
                <div class="flex gap-3 items-start flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center flex-shrink-0 text-xl">🏨</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-bold text-slate-900 dark:text-white">Phòng {{ $order->room->room_number ?? '—' }}</p>
                            <span class="text-[10px] font-mono text-slate-400">#{{ $order->transfer_code }}</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $order->room->zone->name ?? '' }} · {{ $typeLabels[$order->room->type ?? 'standard'] ?? '' }}
                        </p>
                        <div class="flex flex-wrap gap-3 mt-1.5 text-xs">
                            <span class="text-slate-600 dark:text-slate-300">
                                📅 {{ \Carbon\Carbon::parse($order->check_in)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }}
                                <strong>· {{ $nights }} đêm</strong>
                            </span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">
                                💰 {{ number_format($order->total_price) }}đ
                            </span>
                        </div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0
                    {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['dark_bg'] }} {{ $sc['dark_text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                    {{ $sc['label'] }}
                </span>
            </div>

            {{-- Divider --}}
            <div class="border-t border-dashed border-slate-200 dark:border-slate-700 mx-5"></div>

            {{-- Detail --}}
            <div class="px-5 py-4 grid sm:grid-cols-2 gap-4 text-sm">
                <div class="space-y-1.5">
                    @foreach([
                        ['Nhận phòng', \Carbon\Carbon::parse($order->check_in)->format('d/m/Y').' từ 14:00'],
                        ['Trả phòng',  \Carbon\Carbon::parse($order->check_out)->format('d/m/Y').' trước 12:00'],
                        ['Số khách',   $order->total_guests.' người'],
                    ] as [$l, $v])
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $l }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $v }}</span>
                    </div>
                    @endforeach
                    @if($order->note)
                    <div class="pt-1 text-xs text-slate-400 italic">"{{ $order->note }}"</div>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>{{ number_format($order->room->price??0) }}đ × {{ $nights }} đêm</span>
                        <span>{{ number_format(($order->room->price??0)*$nights) }}đ</span>
                    </div>
                    @foreach($svcItems as $svc)
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>{{ $svc->name }} × {{ $svc->quantity }}</span>
                        <span>{{ number_format($svc->price_at_time * $svc->quantity) }}đ</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-1.5 flex justify-between font-bold text-slate-900 dark:text-white">
                        <span>Tổng</span><span>{{ number_format($order->total_price) }}đ</span>
                    </div>
                    @if($order->deposit_amount > 0)
                    <div class="flex justify-between text-amber-600 dark:text-amber-400 text-xs">
                        <span>Đã cọc</span><span class="font-bold">{{ number_format($order->deposit_amount) }}đ</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-5 pb-4 flex flex-wrap gap-2 border-t border-slate-100 dark:border-slate-700 pt-4">
                {{-- Nút xem vé --}}
                @if(in_array($order->status, ['confirmed','checked_in','checked_out']))
                <button onclick="showTicket({{ $order->id }})"
                        class="inline-flex items-center gap-1.5 text-xs font-bold bg-slate-900 dark:bg-white hover:bg-amber-600 dark:hover:bg-amber-500 text-white dark:text-slate-900 dark:hover:text-white px-4 py-2 rounded-xl transition-all">
                    🎫 Xem vé đặt phòng
                </button>
                @endif

                @if($order->status === 'pending' && $order->bill?->qr_image_url)
                <a href="{{ route('booking.payment', $order) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-bold bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-xl transition-colors">
                    💳 Thanh toán ngay
                </a>
                @endif

                @if(in_array($order->status, ['pending']))
                <button onclick="cancelOrder({{ $order->id }}, this)"
                        class="inline-flex items-center gap-1.5 text-xs font-bold border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-2 rounded-xl transition-colors">
                    ✕ Hủy đơn
                </button>
                @endif
            </div>
        </div>
        @endforeach
        @endif

        {{-- Quick links --}}
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-amber-600 border border-slate-200 dark:border-slate-700 hover:border-amber-400 rounded-xl px-4 py-2.5 transition-all">
                👤 Chỉnh sửa hồ sơ
            </a>
            <a href="{{ route('rooms.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-xl px-4 py-2.5 transition-colors">
                🏨 Đặt phòng mới
            </a>
        </div>
    </div>
</div>

{{-- ── TICKET MODAL ── --}}
<div id="ticketModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.75);backdrop-filter:blur(8px)">
    <div class="relative w-full max-w-md">
        {{-- Close --}}
        <button onclick="closeTicket()" class="absolute -top-10 right-0 text-white/70 hover:text-white text-sm font-bold flex items-center gap-1">
            ✕ Đóng
        </button>

        {{-- Ticket card --}}
        <div id="ticketCard" class="bg-white rounded-3xl overflow-hidden shadow-2xl">

            {{-- Header resort --}}
            <div class="px-6 pt-6 pb-4" style="background:linear-gradient(135deg,#0f172a,#1e293b)">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-amber-400 text-[10px] font-bold tracking-[.3em] uppercase">RESORT PRO</p>
                        <p class="text-white font-serif font-bold text-xl mt-0.5">Vé đặt phòng</p>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-400 text-[10px] uppercase tracking-widest">Mã vé</p>
                        <p id="ticketCode" class="text-amber-400 font-mono font-black text-lg tracking-widest"></p>
                    </div>
                </div>
            </div>

            {{-- Tear line --}}
            <div class="relative h-5 flex items-center" style="background:#f8fafc">
                <div class="absolute -left-3 w-6 h-6 rounded-full" style="background:rgba(0,0,0,0.75)"></div>
                <div class="absolute -right-3 w-6 h-6 rounded-full" style="background:rgba(0,0,0,0.75)"></div>
                <div class="w-full border-t-2 border-dashed border-slate-300 mx-4"></div>
            </div>

            {{-- Ticket body --}}
            <div class="px-6 py-4 bg-[#f8fafc]">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Phòng</p>
                        <p id="tRoom" class="font-bold text-slate-900 text-base"></p>
                        <p id="tType" class="text-xs text-slate-500 mt-0.5"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Khu vực</p>
                        <p id="tZone" class="font-bold text-slate-900 text-sm"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Nhận phòng</p>
                        <p id="tCheckIn" class="font-bold text-slate-900 text-sm"></p>
                        <p class="text-xs text-slate-400">Từ 14:00</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Trả phòng</p>
                        <p id="tCheckOut" class="font-bold text-slate-900 text-sm"></p>
                        <p class="text-xs text-slate-400">Trước 12:00</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Số đêm</p>
                        <p id="tNights" class="font-bold text-slate-900 text-sm"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Khách</p>
                        <p id="tGuests" class="font-bold text-slate-900 text-sm"></p>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700">Tổng tiền đặt phòng</p>
                            <p id="tTotal" class="font-black text-amber-700 text-xl mt-0.5"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-amber-600">Đã cọc</p>
                            <p id="tDeposit" class="font-bold text-amber-600 text-sm"></p>
                        </div>
                    </div>
                </div>

                {{-- QR code --}}
                <div class="flex flex-col items-center">
                    <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200">
                        <img id="ticketQR" src="" alt="QR" class="w-36 h-36 object-contain"
                             onerror="this.style.display='none';document.getElementById('ticketQRFallback').style.display='flex'">
                        <div id="ticketQRFallback" style="display:none;width:144px;height:144px;align-items:center;justify-content:center;flex-direction:column;gap:4px">
                            <svg style="width:40px;height:40px;opacity:.3;color:#64748b" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/></svg>
                            <p style="font-size:11px;color:#94a3b8;text-align:center">Xuất trình mã đặt phòng<br>khi check-in</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 text-center">Quét mã tại quầy lễ tân</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-slate-900 flex items-center justify-between">
                <p class="text-slate-400 text-[10px]">Resort Pro · Nha Trang</p>
                <p id="tGuest" class="text-slate-300 text-xs font-semibold"></p>
            </div>
        </div>

        {{-- Download button --}}
        <button onclick="downloadTicket()" id="downloadBtn"
                class="mt-3 w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-2xl text-sm transition-colors flex items-center justify-center gap-2">
            ⬇️ Tải vé về máy
        </button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// ── Orders data cho ticket ─────────────────────────────────────
const ORDERS = {
    @foreach($orders as $order)
    @php
        $svcItems2 = \Illuminate\Support\Facades\DB::table('order_service')
            ->join('services','services.id','=','order_service.service_id')
            ->where('order_service.order_id', $order->id)
            ->select('services.name','order_service.quantity','order_service.price_at_time')
            ->get();
        $nights2 = \Carbon\Carbon::parse($order->check_in)->diffInDays(\Carbon\Carbon::parse($order->check_out));
    @endphp
    {{ $order->id }}: {
        code:      "{{ $order->transfer_code }}",
        room:      "Phòng {{ $order->room->room_number ?? '—' }}",
        type:      "{{ $typeLabels[$order->room->type ?? 'standard'] ?? '' }}",
        zone:      "{{ $order->room->zone->name ?? '' }}",
        checkIn:   "{{ \Carbon\Carbon::parse($order->check_in)->format('d/m/Y') }}",
        checkOut:  "{{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }}",
        nights:    "{{ $nights2 }} đêm",
        guests:    "{{ $order->total_guests }} người",
        total:     "{{ number_format($order->total_price) }}đ",
        deposit:   "{{ number_format($order->deposit_amount) }}đ",
        qr:        "{{ $order->bill?->qr_image_url ?? '' }}",
        guest:     "{{ $user->name }}",
    },
    @endforeach
};

function showTicket(id) {
    const d = ORDERS[id];
    if (!d) return;
    document.getElementById('ticketCode').textContent    = d.code;
    document.getElementById('tRoom').textContent         = d.room;
    document.getElementById('tType').textContent         = d.type;
    document.getElementById('tZone').textContent         = d.zone;
    document.getElementById('tCheckIn').textContent      = d.checkIn;
    document.getElementById('tCheckOut').textContent     = d.checkOut;
    document.getElementById('tNights').textContent       = d.nights;
    document.getElementById('tGuests').textContent       = d.guests;
    document.getElementById('tTotal').textContent        = d.total;
    document.getElementById('tDeposit').textContent      = d.deposit;
    document.getElementById('tGuest').textContent        = d.guest;

    const qrEl      = document.getElementById('ticketQR');
    const qrFallback= document.getElementById('ticketQRFallback');
    // QR check-in luôn dùng transfer_code (không dùng QR MoMo thanh toán)
    const checkInQr = 'https://api.qrserver.com/v1/create-qr-code/?size=144x144&data=' + encodeURIComponent(d.code);
    qrEl.src = checkInQr;
    qrEl.style.display = 'block';
    if (qrFallback) qrFallback.style.display = 'none';

    document.getElementById('ticketModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTicket() {
    document.getElementById('ticketModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Đóng khi click backdrop
document.getElementById('ticketModal').addEventListener('click', function(e) {
    if (e.target === this) closeTicket();
});

// Download vé bằng html2canvas
async function downloadTicket() {
    const btn = document.getElementById('downloadBtn');
    btn.textContent = '⏳ Đang tạo ảnh...';
    btn.disabled = true;
    try {
        const card = document.getElementById('ticketCard');
        const canvas = await html2canvas(card, {
            scale: 2,
            useCORS: true,
            allowTaint: false,
            backgroundColor: '#ffffff',
        });
        const link = document.createElement('a');
        const code = document.getElementById('ticketCode').textContent;
        link.download = `ve-dat-phong-${code}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch(e) {
        alert('Không thể tải ảnh. Vui lòng chụp màn hình.');
    }
    btn.innerHTML = '⬇️ Tải vé về máy';
    btn.disabled = false;
}

// ── Cancel order ───────────────────────────────────────────────
async function cancelOrder(orderId, btn) {
    if (!confirm('Bạn có chắc muốn hủy đặt phòng này không?')) return;
    btn.disabled = true; btn.textContent = 'Đang hủy...';
    try {
        const res  = await fetch(`/booking/${orderId}/cancel`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},
        });
        const data = await res.json();
        if (data.success) location.reload();
        else { btn.disabled=false; btn.textContent='✕ Hủy đơn'; alert(data.message||'Không thể hủy.'); }
    } catch(e) { btn.disabled=false; btn.textContent='✕ Hủy đơn'; }
}
</script>
@endsection