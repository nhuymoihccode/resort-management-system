@extends('layouts.frontend')
@section('title', 'Thanh toán · Resort Pro')
@section('has_hero', 'false')

@section('content')
@php
    $nights    = \Carbon\Carbon::parse($order->check_in)->diffInDays(\Carbon\Carbon::parse($order->check_out));
    $remaining = $order->total_price - $order->deposit_amount;
    $bill      = $order->bill;
    $typeLabels= ['standard'=>'Tiêu Chuẩn','suite'=>'Phòng Cao Cấp','villa'=>'Villa Nguyên Căn','bungalow'=>'Bungalow'];
@endphp

{{-- ── FLOATING CIRCULAR TIMER (fixed top-right) ── --}}
<div id="floatTimer" class="fixed top-20 right-4 z-40" style="width:64px;height:64px;" title="Thời gian giữ phòng còn lại">
    {{-- SVG ring --}}
    <svg style="position:absolute;top:0;left:0;width:64px;height:64px;transform:rotate(-90deg)" viewBox="0 0 64 64">
        <circle cx="32" cy="32" r="28" fill="none" stroke="#334155" stroke-width="5"/>
        <circle id="timerRing" cx="32" cy="32" r="28" fill="none" stroke="#f59e0b" stroke-width="5"
                stroke-dasharray="175.93" stroke-dashoffset="0"
                style="transition:stroke-dashoffset 1s linear"/>
    </svg>
    {{-- Text center --}}
    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#0f172a;border-radius:50%;margin:4px;">
        <p id="floatTimerText" style="color:#f59e0b;font-family:monospace;font-size:11px;font-weight:900;line-height:1;letter-spacing:-0.5px">15:00</p>
        <p style="color:#475569;font-size:7px;line-height:1;margin-top:2px">còn lại</p>
    </div>
</div>

{{-- Alerts --}}
<div id="statusAlert" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 max-w-md w-full px-4">
    <div id="statusAlertInner" class="rounded-2xl p-4 text-sm font-semibold text-center shadow-xl"></div>
</div>
<div id="expiredAlert" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 text-center max-w-sm mx-4 shadow-2xl">
        <div class="text-5xl mb-3">⏰</div>
        <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Hết thời gian giữ phòng</h3>
        <p class="text-slate-500 text-sm mb-5">Đơn đặt phòng đã bị hủy tự động. Vui lòng đặt lại.</p>
        <a href="{{ route('rooms.index') }}" class="bg-amber-600 text-white font-bold px-6 py-3 rounded-xl inline-block hover:bg-amber-500 transition-colors">
            Chọn phòng mới
        </a>
    </div>
</div>

<div class="min-h-screen bg-[#f9f8f6] dark:bg-slate-900 transition-colors duration-300 pt-16">

    {{-- ── HEADER ── --}}
    <div class="bg-slate-900 dark:bg-black pb-5 px-4">
        <div class="max-w-5xl mx-auto pt-6">
            <p class="text-[11px] font-bold tracking-[.25em] uppercase text-amber-400 mb-1">Bước thanh toán</p>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white">Thanh toán đặt cọc</h1>
            <p class="text-slate-400 text-sm mt-1">
                Phòng {{ $order->room->room_number }} · {{ $typeLabels[$order->room->type ?? 'standard'] ?? '' }}
                · {{ \Carbon\Carbon::parse($order->check_in)->format('d/m') }} → {{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }}
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-5">
    <div class="grid lg:grid-cols-5 gap-5">

        {{-- ── CỘT TRÁI (3/5) ── --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- QR Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                {{-- Header --}}
                <div class="bg-slate-950 px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-pink-500 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs font-black">M</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-pink-400 font-bold uppercase tracking-widest">Thanh toán qua MoMo</p>
                            <p class="text-white font-mono font-black text-base tracking-widest">{{ $order->transfer_code }}</p>
                        </div>
                    </div>
                    <button onclick="copyCode()"
                            class="text-xs text-amber-400 border border-amber-400/30 hover:bg-amber-400/10 px-3 py-1.5 rounded-lg transition-colors font-bold">
                        Sao chép
                    </button>
                </div>

                {{-- QR + Info row --}}
                <div class="p-5 flex flex-col sm:flex-row gap-5 items-center sm:items-start">

                    {{-- QR --}}
                    <div class="flex flex-col items-center flex-shrink-0">
                        <div class="relative bg-white rounded-2xl p-2.5 shadow-md border-2 border-pink-100">
                            @if($bill?->qr_image_url)
                            <img id="qrImg" src="{{ $bill->qr_image_url }}" alt="QR MoMo"
                                 class="w-48 h-48 object-contain rounded-xl">
                            @else
                            <div id="qrImg" class="w-48 h-48 flex flex-col items-center justify-center text-slate-300 text-xs text-center gap-2 p-3">
                                <div class="w-8 h-8 border-2 border-slate-300 border-t-pink-500 rounded-full animate-spin"></div>
                                Đang tạo QR...
                            </div>
                            @endif
                            {{-- Confirmed overlay --}}
                            <div id="qrOverlay" class="hidden absolute inset-0 rounded-2xl bg-green-500/95 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <svg class="w-14 h-14 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="font-bold">Đã xác nhận!</p>
                                </div>
                            </div>
                        </div>
                        <button onclick="refreshQR()" id="refreshQRBtn"
                                class="mt-2.5 text-[11px] text-slate-400 hover:text-pink-500 flex items-center gap-1 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Làm mới QR
                        </button>
                    </div>

                    {{-- Info bên phải QR --}}
                    <div class="flex-1 space-y-3 w-full">
                        <div class="bg-pink-500 rounded-2xl px-4 py-3 text-center">
                            <p class="text-pink-100 text-[10px] font-bold uppercase tracking-widest mb-0.5">Số tiền MoMo</p>
                            <p class="text-white text-3xl font-black">{{ number_format($order->deposit_amount) }}đ</p>
                            <p class="text-pink-200 text-xs mt-0.5">Đặt cọc 30%</p>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-3 space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Phòng</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->room->room_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Check-in</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($order->check_in)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Check-out</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 dark:border-slate-600 pt-1.5">
                                <span class="text-slate-500">Tổng cộng</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ number_format($order->total_price) }}đ</span>
                            </div>
                        </div>

                        <div class="text-xs text-slate-400 bg-amber-50 dark:bg-amber-900/20 rounded-xl px-3 py-2.5 leading-relaxed">
                            <strong class="text-amber-700 dark:text-amber-400">Hướng dẫn:</strong>
                            Mở app <strong class="text-pink-500">MoMo</strong> → Quét mã QR → Xác nhận thanh toán.
                            <br>Chỉ dùng app <strong>MoMo phiên bản thử nghiệm</strong> để test sandbox.
                        </div>
                    </div>
                </div>

                {{-- Timer bar --}}
                <div class="px-5 pb-4">
                    <div class="flex justify-between text-xs text-slate-400 mb-1">
                        <span>Thời gian giữ phòng</span>
                        <span id="timerLabel" class="font-mono font-bold text-amber-600"></span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div id="timerBar" class="h-1.5 rounded-full bg-amber-500 transition-all duration-1000" style="width:100%"></div>
                    </div>
                </div>
            </div>

            {{-- Demo + Hướng dẫn --}}
            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Demo button --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 p-4 flex flex-col items-center text-center gap-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Chế độ Demo</p>
                    <button onclick="simulatePayment()" id="simulateBtn"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Giả lập nhận tiền
                    </button>
                    <p class="text-[10px] text-slate-300 dark:text-slate-600">Thay bằng webhook khi production</p>
                </div>

                {{-- Steps --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 space-y-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">Các bước</p>
                    @foreach(['Mở app MoMo sandbox','Quét mã QR bên trái','Xác nhận thanh toán','Nhận xác nhận qua email'] as $i => $step)
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 min-w-[1.25rem] rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-[10px] font-black flex items-center justify-center">{{ $i+1 }}</span>
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── CỘT PHẢI (2/5) ── --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden sticky top-24">

                {{-- Room --}}
                <div class="bg-slate-900 dark:bg-slate-950 p-4 flex gap-3 items-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Phòng {{ $order->room->room_number }}</p>
                        <p class="text-slate-400 text-xs">{{ $typeLabels[$order->room->type ?? 'standard'] ?? '' }} · {{ $order->room->zone->name ?? '' }}</p>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    {{-- Chi tiết giá --}}
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>{{ number_format($order->room->price ?? 0) }}đ × {{ $nights }} đêm</span>
                            <span>{{ number_format(($order->room->price ?? 0) * $nights) }}đ</span>
                        </div>
                        @foreach($services as $svc)
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span class="truncate pr-2">{{ $svc->name }} × {{ $svc->quantity }}</span>
                            <span class="flex-shrink-0">{{ number_format($svc->price_at_time * $svc->quantity) }}đ</span>
                        </div>
                        @endforeach
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-2 flex justify-between font-bold text-slate-900 dark:text-white">
                            <span>Tổng cộng</span>
                            <span>{{ number_format($order->total_price) }}đ</span>
                        </div>
                    </div>

                    {{-- Đặt cọc --}}
                    <div class="rounded-xl overflow-hidden">
                        <div class="bg-amber-500 px-4 py-3">
                            <p class="text-amber-100 text-[10px] font-bold uppercase tracking-widest mb-0.5">Cần thanh toán</p>
                            <p class="text-white text-2xl font-black">{{ number_format($order->deposit_amount) }}đ</p>
                        </div>
                        <div class="bg-amber-600 px-4 py-2 flex justify-between text-xs">
                            <span class="text-amber-100">Còn lại khi check-in</span>
                            <span class="text-white font-bold">{{ number_format($remaining) }}đ</span>
                        </div>
                    </div>

                    @if($order->note)
                    <div class="text-xs text-slate-400 italic bg-slate-50 dark:bg-slate-700/40 rounded-xl px-3 py-2">"{{ $order->note }}"</div>
                    @endif

                    <button onclick="cancelOrder()" id="cancelBtn"
                            class="w-full text-sm text-slate-400 hover:text-red-500 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-red-200 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all font-medium">
                        Hủy đặt phòng
                    </button>
                    <p class="text-[10px] text-center text-slate-300 dark:text-slate-600">
                        Đã chuyển khoản nhưng chưa thấy xác nhận? Liên hệ hotline.
                    </p>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>

<script>
const EXPIRES_AT    = new Date('{{ $order->expires_at->toISOString() }}');
const POLL_URL      = '{{ route("booking.status", $order) }}';
const CANCEL_URL    = '{{ route("booking.cancel", $order) }}';
const CONFIRM_URL   = '{{ route("admin.booking.confirm-payment", $order) }}';
const REFRESH_QR    = '{{ route("booking.refresh-qr", $order) }}';
const DASHBOARD_URL = '{{ route("dashboard") }}';
const CSRF          = '{{ csrf_token() }}';
const TOTAL_SECS    = Math.max(1, Math.round((EXPIRES_AT - new Date()) / 1000));
const RING_CIRC     = 175.93; // 2 * π * 28

// ── Circular Timer ─────────────────────────────────────────────
const ring        = document.getElementById('timerRing');
const floatText   = document.getElementById('floatTimerText');
const timerLabel  = document.getElementById('timerLabel');
const timerBar    = document.getElementById('timerBar');
const floatTimer  = document.getElementById('floatTimer');

function updateTimer() {
    const left = Math.max(0, Math.round((EXPIRES_AT - new Date()) / 1000));
    const m = String(Math.floor(left/60)).padStart(2,'0');
    const s = String(left%60).padStart(2,'0');
    const txt = m+':'+s;

    if (floatText)  floatText.textContent  = txt;
    if (timerLabel) timerLabel.textContent = txt;

    // Ring progress
    const pct = left / TOTAL_SECS;
    if (ring) ring.style.strokeDashoffset = RING_CIRC * (1 - pct);
    if (timerBar) timerBar.style.width = (pct * 100) + '%';

    // Color warning
    if (left <= 120) {
        if (ring) ring.style.stroke = '#ef4444';
        if (floatTimer) { floatTimer.style.borderColor = '#ef4444'; }
        if (timerBar) timerBar.classList.replace('bg-amber-500','bg-red-500');
        if (floatText) floatText.classList.add('text-red-400');
    }

    if (left === 0) {
        clearAll();
        document.getElementById('expiredAlert')?.classList.remove('hidden');
        disableAll();
    }
}
const timerInterval = setInterval(updateTimer, 1000);
updateTimer();

// ── Polling ────────────────────────────────────────────────────
const pollInterval = setInterval(async () => {
    try {
        const data = await (await fetch(POLL_URL, {headers:{'X-Requested-With':'XMLHttpRequest'}})).json();
        if (data.status === 'confirmed' || data.confirm_status === 'confirmed') {
            clearAll();
            document.getElementById('qrOverlay')?.classList.remove('hidden');
            showAlert('success', '🎉 Thanh toán xác nhận! Đang chuyển về dashboard...');
            disableAll();
            setTimeout(() => window.location.href = DASHBOARD_URL, 3000);
        }
    } catch(e) {}
}, 5000);

// ── Giả lập ────────────────────────────────────────────────────
async function simulatePayment() {
    if (!confirm('Giả lập xác nhận đã nhận tiền?')) return;
    const btn = document.getElementById('simulateBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Đang xử lý...';
    try {
        const data = await (await fetch(CONFIRM_URL, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json'},
        })).json();
        if (data.success) {
            clearAll();
            document.getElementById('qrOverlay')?.classList.remove('hidden');
            showAlert('success', '✅ Xác nhận thành công! Email đã gửi.');
            disableAll();
            setTimeout(() => window.location.href = DASHBOARD_URL, 3000);
        } else {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Giả lập nhận tiền';
            showAlert('error', data.message || 'Lỗi xác nhận.');
        }
    } catch(e) {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Giả lập nhận tiền';
        showAlert('error', 'Lỗi kết nối.');
    }
}

// ── Refresh QR ─────────────────────────────────────────────────
async function refreshQR() {
    const btn = document.getElementById('refreshQRBtn');
    if (btn) { btn.disabled=true; btn.textContent='Đang tạo...'; }
    try {
        const data = await (await fetch(REFRESH_QR, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
        })).json();
        if (data.success && data.qr_url) {
            const img = document.getElementById('qrImg');
            if (img && img.tagName === 'IMG') {
                img.src = data.qr_url + '?t=' + Date.now();
            } else if (img) {
                const newImg = document.createElement('img');
                newImg.id = 'qrImg';
                newImg.src = data.qr_url + '?t=' + Date.now();
                newImg.alt = 'QR MoMo';
                newImg.className = 'w-48 h-48 object-contain rounded-xl';
                img.parentNode.replaceChild(newImg, img);
            }
            showAlert('info', 'QR mới đã được tạo! Quét ngay trong 2 phút.');
        } else {
            showAlert('error', data.message || 'Không thể tạo QR mới.');
        }
    } catch(e) { showAlert('error', 'Lỗi kết nối.'); }
    if (btn) { btn.disabled=false; btn.innerHTML='<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Làm mới QR'; }
}
setInterval(refreshQR, 90000);

// ── Hủy ────────────────────────────────────────────────────────
async function cancelOrder() {
    if (!confirm('Hủy đặt phòng này?')) return;
    const btn = document.getElementById('cancelBtn');
    btn.disabled=true; btn.textContent='Đang hủy...';
    try {
        const data = await (await fetch(CANCEL_URL, {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}
        })).json();
        if (data.success) { clearAll(); showAlert('error','Đã hủy. Đang chuyển hướng...'); setTimeout(()=>window.location.href='{{ route("rooms.index") }}',2500); }
        else { btn.disabled=false; btn.textContent='Hủy đặt phòng'; showAlert('error',data.message||'Không thể hủy.'); }
    } catch(e) { btn.disabled=false; btn.textContent='Hủy đặt phòng'; }
}

function copyCode() {
    navigator.clipboard.writeText('{{ $order->transfer_code }}').then(()=>{
        showAlert('info','Đã sao chép: {{ $order->transfer_code }}');
        setTimeout(()=>document.getElementById('statusAlert')?.classList.add('hidden'),2000);
    });
}

function disableAll() { ['cancelBtn','simulateBtn','refreshQRBtn'].forEach(id=>{const el=document.getElementById(id);if(el)el.disabled=true;}); }
function clearAll()   { clearInterval(pollInterval); clearInterval(timerInterval); }
function showAlert(type, msg) {
    const wrap  = document.getElementById('statusAlert');
    const inner = document.getElementById('statusAlertInner');
    const map   = {
        success:'bg-green-500 text-white shadow-green-500/30',
        error:'bg-red-500 text-white shadow-red-500/30',
        info:'bg-blue-500 text-white shadow-blue-500/30',
    };
    inner.className = 'rounded-2xl p-4 text-sm font-semibold text-center shadow-xl ' + (map[type]||map.info);
    inner.textContent = msg;
    wrap.classList.remove('hidden');
    if (type === 'info') setTimeout(()=>wrap.classList.add('hidden'), 3000);
}
</script>
@endsection