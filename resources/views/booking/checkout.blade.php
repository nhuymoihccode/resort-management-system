@extends('layouts.frontend')
@section('title', 'Xác nhận đặt phòng · Resort Pro')
@section('has_hero', 'false')

@section('content')
@php
    $typeLabels    = ['standard'=>'Tiêu Chuẩn','suite'=>'Phòng Cao Cấp','villa'=>'Villa Nguyên Căn','bungalow'=>'Phòng Gia Đình'];
    $roomImages    = ['standard'=>'https://images.pexels.com/photos/271618/pexels-photo-271618.jpeg?auto=compress&w=800','suite'=>'https://images.pexels.com/photos/1457842/pexels-photo-1457842.jpeg?auto=compress&w=800','villa'=>'https://images.pexels.com/photos/261102/pexels-photo-261102.jpeg?auto=compress&w=800','bungalow'=>'https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&w=800'];
    $roomTotal     = $room->price * $nights;
    $depositAmount = (int)ceil($roomTotal * 0.3);
    $checkInFmt    = \Carbon\Carbon::parse($checkIn)->locale('vi')->isoFormat('dddd, D/MM/YYYY');
    $checkOutFmt   = \Carbon\Carbon::parse($checkOut)->locale('vi')->isoFormat('dddd, D/MM/YYYY');
    $heroImg       = $roomImages[$room->type] ?? $roomImages['standard'];
    $serviceIcons  = ['turn'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z','person'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','hour'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
@endphp

{{-- ── STICKY TIMER BAR ── --}}
<div id="stickyTimer"
     class="fixed top-0 left-0 right-0 z-50 bg-amber-600 text-white px-4 py-2 flex items-center justify-center gap-3 shadow-lg transition-all duration-300"
     style="transform:translateY(-100%)">
    <svg class="w-4 h-4 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="text-sm font-bold tracking-wide">Phòng đang được giữ cho bạn —</span>
    <span id="stickyTimerText" class="text-lg font-mono font-black tracking-widest">15:00</span>
    <span class="text-sm opacity-75">còn lại</span>
</div>

{{-- ── HERO PHÒNG ── --}}
<div class="relative h-64 sm:h-80 overflow-hidden" style="margin-top:0">
    <img src="{{ $heroImg }}" alt="Phòng {{ $room->room_number }}"
         class="w-full h-full object-cover" style="object-position:center 40%">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

    {{-- Overlay info --}}
    <div class="absolute inset-x-0 bottom-0 px-6 pb-6 flex items-end justify-between gap-4">
        <div>
            <span class="inline-block bg-amber-600 text-white text-[10px] font-bold uppercase tracking-[.15em] px-3 py-1 rounded-full mb-2">
                {{ $typeLabels[$room->type] ?? $room->type }}
            </span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-white leading-none">
                Phòng {{ $room->room_number }}
            </h2>
            <p class="text-slate-300 text-sm mt-1">{{ $room->zone->name ?? '' }} · {{ $room->view ?? '' }}</p>
        </div>
        {{-- Timer trên hero --}}
        <div id="heroTimer"
             class="flex-shrink-0 bg-black/50 backdrop-blur-md border border-amber-500/40 rounded-2xl px-4 py-3 text-center min-w-[100px]">
            <p class="text-[9px] font-bold uppercase tracking-[.15em] text-amber-400 mb-0.5">Còn lại</p>
            <p id="heroTimerText" class="text-2xl font-mono font-black text-white tracking-wider">15:00</p>
            <p class="text-[9px] text-slate-400 mt-0.5">giữ phòng</p>
        </div>
    </div>
</div>

{{-- ── DATES BANNER ── --}}
<div class="bg-slate-900 dark:bg-black px-6 py-4">
    <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-6">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-amber-400 mb-0.5">Nhận phòng</p>
                <p class="text-white font-semibold text-sm">{{ $checkInFmt }}</p>
                <p class="text-slate-400 text-xs">Từ 14:00</p>
            </div>
            <div class="flex flex-col items-center gap-1">
                <div class="w-16 h-px bg-slate-700"></div>
                <span class="text-amber-500 text-xs font-bold whitespace-nowrap">{{ $nights }} đêm</span>
                <div class="w-16 h-px bg-slate-700"></div>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[.15em] text-amber-400 mb-0.5">Trả phòng</p>
                <p class="text-white font-semibold text-sm">{{ $checkOutFmt }}</p>
                <p class="text-slate-400 text-xs">Trước 12:00</p>
            </div>
        </div>
        <a href="{{ route('booking.cancel-holding', $holdOrder) }}"
           class="text-xs text-slate-400 hover:text-amber-400 transition-colors border border-slate-700 hover:border-amber-500 px-3 py-1.5 rounded-lg">
            Đổi ngày
        </a>
    </div>
</div>

{{-- ── EXPIRED ALERT ── --}}
<div id="expiredAlert" class="hidden bg-red-600 text-white px-6 py-4 text-center font-semibold">
    ⏰ Phiên giữ phòng đã hết hạn.
    <a href="{{ route('rooms.show', $room->id) }}" class="underline ml-2">Chọn lại ngày →</a>
</div>

{{-- ── MAIN CONTENT ── --}}
<div class="bg-[#f9f8f6] dark:bg-slate-900 pb-24 transition-colors duration-300">
<div class="max-w-5xl mx-auto px-4 sm:px-6 pt-8">
<div class="grid lg:grid-cols-5 gap-6">

    {{-- ── CỘT TRÁI (3/5) ── --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Dịch vụ thêm --}}
        @if($services->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Dịch vụ thêm</p>
                    <p class="text-xs text-slate-400">Nâng cấp trải nghiệm của bạn</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @foreach($services as $service)
                @php $icon = $serviceIcons[$service->unit] ?? $serviceIcons['turn']; @endphp
                <label class="flex items-center gap-4 px-5 py-4 hover:bg-amber-50/60 dark:hover:bg-amber-900/10 cursor-pointer transition-all duration-150 service-row group"
                       data-price="{{ $service->price }}">
                    {{-- Checkbox custom --}}
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" value="{{ $service->id }}"
                               class="service-check sr-only" data-price="{{ $service->price }}">
                        <div class="check-box w-5 h-5 rounded-md border-2 border-slate-300 dark:border-slate-600 group-hover:border-amber-400 transition-colors flex items-center justify-center">
                            <svg class="check-icon w-3 h-3 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    {{-- Icon dịch vụ --}}
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 transition-colors">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    {{-- Tên + giá --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">{{ $service->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span class="font-bold text-slate-600 dark:text-slate-300">{{ number_format($service->price) }}đ</span>
                            / {{ $service->unit === 'person' ? 'người' : ($service->unit === 'hour' ? 'giờ' : 'lần') }}
                        </p>
                    </div>
                    {{-- Qty --}}
                    <div class="qty-wrap hidden items-center gap-1.5">
                        <button type="button" onclick="changeQty(this,-1)"
                                class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-base transition-colors">−</button>
                        <input type="number" value="1" min="1" max="99"
                               class="qty-input w-9 text-center text-sm font-bold bg-transparent border-0 outline-none text-slate-800 dark:text-slate-100" style="-moz-appearance:textfield;-webkit-appearance:none;" oninput="updateTotals()">
                        <button type="button" onclick="changeQty(this,1)"
                                class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-base transition-colors">+</button>
                    </div>
                    <span class="svc-price hidden text-sm font-bold text-amber-600 dark:text-amber-400 min-w-[76px] text-right"></span>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Số khách + Ghi chú --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Thông tin khách</p>
                    <p class="text-xs text-slate-400">Tối đa {{ $room->capacity_adults ?? 2 }} người lớn</p>
                </div>
            </div>
            <div class="p-5 space-y-5">
                {{-- Số khách --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Số khách</p>
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="changeGuests(-1)"
                                class="w-11 h-11 rounded-2xl border-2 border-slate-200 dark:border-slate-600 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-600 dark:text-slate-300 font-bold text-xl flex items-center justify-center transition-all">−</button>
                        <div class="text-center">
                            <span id="guestCount" class="text-3xl font-bold text-slate-900 dark:text-white">2</span>
                            <span class="text-slate-400 text-sm ml-1">người</span>
                        </div>
                        <button type="button" onclick="changeGuests(1)"
                                class="w-11 h-11 rounded-2xl border-2 border-slate-200 dark:border-slate-600 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-slate-600 dark:text-slate-300 font-bold text-xl flex items-center justify-center transition-all">+</button>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">
                        Ghi chú <span class="font-normal normal-case tracking-normal text-slate-300 dark:text-slate-600">(tùy chọn)</span>
                    </p>
                    <textarea id="noteInput" rows="3" maxlength="500"
                              placeholder="Ví dụ: phòng tầng cao, đến sau 22h, cần giường phụ..."
                              class="w-full text-sm bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 transition resize-none leading-relaxed"></textarea>
                    <p class="text-xs text-slate-300 dark:text-slate-600 mt-1.5 text-right"><span id="noteCount">0</span>/500</p>
                </div>
            </div>
        </div>

        {{-- Chính sách --}}
        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/40 rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400 mb-3">Chính sách đặt & hủy</p>
            <div class="space-y-2.5 text-sm text-slate-600 dark:text-slate-300">
                @foreach([
                    ['emerald','M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','Đặt cọc 30% khi xác nhận — phần còn lại thanh toán khi nhận phòng'],
                    ['emerald','M5 13l4 4L19 7','Hủy miễn phí trong 24 giờ đầu sau khi đặt'],
                    ['rose','M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z','Hủy sau 24 giờ hoặc trước 7 ngày nhận phòng — mất cọc'],
                    ['rose','M6 18L18 6M6 6l12 12','Không hoàn tiền nếu hủy dưới 48 giờ trước nhận phòng'],
                ] as [$color, $icon, $text])
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-{{ $color }}-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                    <span class="leading-relaxed">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── CỘT PHẢI (2/5) ── --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-6">

            {{-- Header tóm tắt --}}
            <div class="bg-slate-900 dark:bg-slate-950 px-5 py-4">
                <p class="text-[10px] font-bold uppercase tracking-[.2em] text-slate-400 mb-0.5">Tóm tắt đặt phòng</p>
                <p class="text-white font-serif font-bold text-lg">Phòng {{ $room->room_number }}</p>
                <p class="text-slate-400 text-xs capitalize">{{ $typeLabels[$room->type] ?? $room->type }}</p>
            </div>

            <div class="p-5 space-y-4">

                {{-- Chi tiết giá --}}
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>{{ number_format($room->price) }}đ × {{ $nights }} đêm</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($roomTotal) }}đ</span>
                    </div>
                    <div id="svcRow" class="hidden flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Dịch vụ thêm</span>
                        <span id="svcTotal" class="font-semibold text-slate-700 dark:text-slate-300"></span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 dark:border-slate-700 pt-2 flex justify-between font-bold text-base text-slate-900 dark:text-white">
                        <span>Tổng cộng</span>
                        <span id="grandTotal">{{ number_format($roomTotal) }}đ</span>
                    </div>
                </div>

                {{-- Đặt cọc highlight --}}
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-4 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80 mb-1">Cần thanh toán ngay</p>
                    <p id="depositDisplay" class="text-3xl font-black tracking-tight">{{ number_format($depositAmount) }}đ</p>
                    <p class="text-xs opacity-75 mt-1">Đặt cọc 30% · giữ phòng ngay</p>
                    <div class="mt-3 pt-3 border-t border-white/20 flex justify-between text-xs">
                        <span class="opacity-75">Còn lại khi check-in</span>
                        <span id="remainDisplay" class="font-bold">{{ number_format($roomTotal - $depositAmount) }}đ</span>
                    </div>
                </div>

                {{-- Nút xác nhận --}}
                <button id="confirmBtn" onclick="submitBooking()"
                        class="w-full bg-slate-900 dark:bg-white hover:bg-amber-600 dark:hover:bg-amber-500 text-white dark:text-slate-900 dark:hover:text-white font-bold py-4 rounded-2xl text-sm uppercase tracking-widest shadow-lg transition-all duration-300 active:scale-[.98] flex items-center justify-center gap-2.5 group disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Xác nhận đặt phòng
                </button>

                {{-- Trust signals --}}
                <div class="space-y-1.5 pt-1">
                    @foreach(['Phòng đang được giữ 15 phút cho bạn','Email xác nhận gửi tức thì','Không có phí ẩn'] as $t)
                    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                        <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $t }}
                    </div>
                    @endforeach
                </div>

                <p class="text-[10px] text-center text-slate-300 dark:text-slate-600 leading-relaxed">
                    Bằng cách xác nhận, bạn đồng ý với chính sách đặt phòng của Resort Pro.
                </p>
            </div>
        </div>
    </div>

</div>
</div>
</div>

{{-- Loading overlay --}}
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 text-center shadow-2xl max-w-xs w-full mx-4">
        <div class="w-14 h-14 border-4 border-amber-100 border-t-amber-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="font-serif font-bold text-slate-900 dark:text-white text-lg mb-1">Đang xử lý</p>
        <p class="text-sm text-slate-400">Đang tạo đơn đặt phòng của bạn...</p>
    </div>
</div>

<style>
.service-check:checked ~ .check-box,
.check-box.checked {
    background: #d97706;
    border-color: #d97706;
}
.service-check:checked ~ .check-box .check-icon { display: block; }
</style>

<script>
const ROOM_PRICE    = {{ $room->price }};
const NIGHTS        = {{ $nights }};
const ROOM_TOTAL    = {{ $roomTotal }};
const STORE_URL     = '{{ route('booking.store') }}';
const CSRF          = '{{ csrf_token() }}';
const HOLD_ORDER_ID = {{ $holdOrder->id }};
const EXPIRES_AT    = new Date('{{ $holdOrder->expires_at->toISOString() }}');
const TOTAL_SECS    = Math.max(1, Math.round((EXPIRES_AT - new Date()) / 1000));
let expired = false;

// ── Timer ─────────────────────────────────────────────────────
function updateTimers() {
    const left = Math.max(0, Math.round((EXPIRES_AT - new Date()) / 1000));
    const m = String(Math.floor(left/60)).padStart(2,'0');
    const s = String(left%60).padStart(2,'0');
    const txt = m+':'+s;
    document.getElementById('heroTimerText').textContent  = txt;
    document.getElementById('stickyTimerText').textContent = txt;

    // Sticky bar xuất hiện sau khi scroll qua hero timer
    const heroTimerEl = document.getElementById('heroTimer');
    const stickyBar   = document.getElementById('stickyTimer');
    if (heroTimerEl) {
        const rect = heroTimerEl.getBoundingClientRect();
        if (rect.bottom < 0) {
            stickyBar.style.transform = 'translateY(0)';
        } else {
            stickyBar.style.transform = 'translateY(-100%)';
        }
    }

    if (left <= 60) {
        ['heroTimerText','stickyTimerText'].forEach(id=>{
            const el=document.getElementById(id);
            if(el){el.classList.add('text-red-300');el.classList.remove('text-white');}
        });
        document.getElementById('stickyTimer').classList.replace('bg-amber-600','bg-red-600');
    }

    if (left === 0 && !expired) {
        expired = true;
        clearInterval(timerInterval);
        document.getElementById('expiredAlert').classList.remove('hidden');
        document.getElementById('confirmBtn').disabled = true;
    }
}
const timerInterval = setInterval(updateTimers, 1000);
updateTimers();
window.addEventListener('scroll', updateTimers, {passive:true});

// ── Checkbox custom styling ────────────────────────────────────
document.querySelectorAll('.service-check').forEach(cb => {
    cb.addEventListener('change', function() {
        const row     = this.closest('.service-row');
        const box     = row.querySelector('.check-box');
        const icon    = row.querySelector('.check-icon');
        const qtyWrap = row.querySelector('.qty-wrap');
        const svcPrice= row.querySelector('.svc-price');
        if (this.checked) {
            box.style.background = '#d97706';
            box.style.borderColor= '#d97706';
            icon.classList.remove('hidden');
            qtyWrap.classList.remove('hidden');
            qtyWrap.classList.add('flex');
            svcPrice.classList.remove('hidden');
        } else {
            box.style.background = '';
            box.style.borderColor= '';
            icon.classList.add('hidden');
            qtyWrap.classList.add('hidden');
            qtyWrap.classList.remove('flex');
            svcPrice.classList.add('hidden');
        }
        updateTotals();
    });
});

// ── Qty ───────────────────────────────────────────────────────
function changeQty(btn, d) {
    const input = btn.closest('.service-row').querySelector('.qty-input');
    input.value = Math.max(1, Math.min(99, parseInt(input.value)+d));
    updateTotals();
}

// ── Totals ────────────────────────────────────────────────────
function updateTotals() {
    let svc = 0;
    document.querySelectorAll('.service-check:checked').forEach(cb => {
        const row = cb.closest('.service-row');
        const qty = parseInt(row.querySelector('.qty-input')?.value||1);
        const p   = parseInt(cb.dataset.price);
        svc += p*qty;
        row.querySelector('.svc-price').textContent = (p*qty).toLocaleString('vi-VN')+'đ';
    });
    const grand   = ROOM_TOTAL + svc;
    const deposit = Math.ceil(grand*0.3);
    document.getElementById('grandTotal').textContent    = grand.toLocaleString('vi-VN')+'đ';
    document.getElementById('depositDisplay').textContent= deposit.toLocaleString('vi-VN')+'đ';
    document.getElementById('remainDisplay').textContent = (grand-deposit).toLocaleString('vi-VN')+'đ';
    const row = document.getElementById('svcRow');
    if (svc>0) { row.classList.remove('hidden'); document.getElementById('svcTotal').textContent=svc.toLocaleString('vi-VN')+'đ'; }
    else        { row.classList.add('hidden'); }
}

// ── Guests ────────────────────────────────────────────────────
let guestCount = 2;
const maxGuests = {{ $room->capacity_adults ?? 2 }};
function changeGuests(d) {
    guestCount = Math.max(1, Math.min(maxGuests, guestCount+d));
    document.getElementById('guestCount').textContent = guestCount;
}

// ── Note counter ──────────────────────────────────────────────
document.getElementById('noteInput').addEventListener('input', function(){
    document.getElementById('noteCount').textContent = this.value.length;
});

// ── Submit ────────────────────────────────────────────────────
async function submitBooking() {
    if (expired) return;
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    document.getElementById('loadingOverlay').classList.remove('hidden');

    const services = [];
    document.querySelectorAll('.service-check:checked').forEach(cb=>{
        const qty=parseInt(cb.closest('.service-row').querySelector('.qty-input')?.value||1);
        services.push({id:parseInt(cb.value),qty});
    });

    try {
        const res  = await fetch(STORE_URL,{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({hold_order_id:HOLD_ORDER_ID,total_guests:guestCount,note:document.getElementById('noteInput').value.trim(),services}),
        });
        const data = await res.json();
        if (!data.success) {
            document.getElementById('loadingOverlay').classList.add('hidden');
            btn.disabled = false;
            if (data.expired) { document.getElementById('expiredAlert').classList.remove('hidden'); btn.disabled=true; }
            else alert(data.message||'Có lỗi xảy ra.');
            return;
        }
        window.location.href = data.redirect_url;
    } catch(e) {
        document.getElementById('loadingOverlay').classList.add('hidden');
        btn.disabled = false;
        alert('Lỗi kết nối. Vui lòng thử lại.');
    }
}
</script>
@endsection