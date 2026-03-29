@extends('layouts.frontend')

@section('title', 'Danh sách Phòng & Villa | Resort Pro')
@section('has_hero', 'false')

@section('content')

{{-- ── Page header — ảnh nền + overlay gradient ── --}}
<div class="relative pt-24 pb-16 overflow-hidden"
     style="background: url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">
    {{-- Overlay tối để chữ luôn đọc được cả light lẫn dark mode --}}
    <div class="absolute inset-0 bg-slate-950/60"></div>
    {{-- Fade xuống cuối --}}
    <div class="absolute bottom-0 inset-x-0 h-16 bg-gradient-to-b from-transparent to-slate-950/50"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-400 font-bold tracking-[0.25em] uppercase text-xs mb-4">Không gian lưu trú</p>
        <h1 class="text-4xl md:text-6xl font-serif text-white mb-4 drop-shadow-lg">
            Khám phá không gian lưu trú
        </h1>
        <p class="text-slate-200 max-w-xl mx-auto text-base leading-relaxed">
            Lựa chọn hoàn hảo cho kỳ nghỉ dưỡng của bạn với các tiện nghi đẳng cấp và tầm nhìn tuyệt mỹ.
        </p>
    </div>
</div>

{{-- ── SELECTED DATE DISPLAY (nếu có search từ welcome) ── --}}
@if($checkIn && $checkOut)
@php
    $checkInDate = \Carbon\Carbon::parse($checkIn);
    $checkOutDate = \Carbon\Carbon::parse($checkOut);
    $nights = $checkInDate->diffInDays($checkOutDate);
@endphp
<div class="bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm font-semibold text-emerald-800 dark:text-emerald-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>
                <strong>{{ $checkInDate->format('d/m/Y') }}</strong>
                →
                <strong>{{ $checkOutDate->format('d/m/Y') }}</strong>
                <span class="text-emerald-600 dark:text-emerald-400">({{ $nights }} đêm)</span>
            </span>
        </div>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold px-4 py-2 rounded-lg transition-all duration-200 active:scale-95 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Thay đổi ngày
        </a>
    </div>
</div>
@endif

{{-- ── FILTER BAR ── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form id="filterForm" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-300 dark:border-slate-700 p-6 shadow-md dark:shadow-none transition-colors duration-300">
        
        {{-- Date được lưu trong session, không truyền qua URL --}}
        {{-- Hidden inputs thực sự gửi đi (số nguyên) --}}
        <input type="hidden" name="min_price" id="min_price_hidden" value="{{ $minPrice }}">
        <input type="hidden" name="max_price" id="max_price_hidden" value="{{ $maxPrice }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            
            {{-- Filter: Loại phòng --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-2">
                    Loại phòng
                </label>
                <select name="type" class="w-full px-3 py-2.5 border-2 border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-colors">
                    <option value="">Tất cả loại</option>
                    <option value="standard" @if($type === 'standard') selected @endif>Tiêu Chuẩn</option>
                    <option value="suite" @if($type === 'suite') selected @endif>Phòng Cao Cấp</option>
                    <option value="villa" @if($type === 'villa') selected @endif>Villa Nguyên Căn</option>
                    <option value="bungalow" @if($type === 'bungalow') selected @endif>Phòng Gia Đình</option>
                </select>
            </div>

            {{-- ── FIX 1: Sức chứa — icon đúng, bỏ "4+" vì max thực tế là 4 ── --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-2">
                    Sức chứa
                </label>
                <select name="capacity" class="w-full px-3 py-2.5 border-2 border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-colors">
                    <option value="">Tất cả</option>
                    {{-- 1 người: icon đơn --}}
                    <option value="1" @if($capacity === '1') selected @endif>🧍 1 người</option>
                    {{-- 2 người: icon đôi --}}
                    <option value="2" @if($capacity === '2') selected @endif>👫 2 người</option>
                    {{-- 3 người: 3 icon --}}
                    <option value="3" @if($capacity === '3') selected @endif>👨‍👩‍👦 3 người</option>
                    {{-- 4 người: đúng là "4 người", không phải "4+" vì max là 4 --}}
                    <option value="4" @if($capacity === '4') selected @endif>👨‍👩‍👧‍👦 4 người (Gia đình)</option>
                </select>
            </div>

            {{-- ── FIX 2 & 3: Giá tối thiểu — hiển thị formatted, custom stepper ── --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-2">
                    Giá tối thiểu
                </label>
                {{-- Input hiển thị (formatted, không submit) --}}
                <div class="flex rounded-lg border-2 border-slate-300 dark:border-slate-600 overflow-hidden focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-amber-500 transition-all bg-white dark:bg-slate-700">
                    <button type="button" onclick="stepPrice('min', -1)"
                        class="px-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-600 hover:text-amber-600 transition-colors text-lg font-bold select-none">−</button>
                    <input type="text" id="min_price_display"
                        value="{{ $minPrice ? number_format($minPrice, 0, ',', '.') : '' }}"
                        placeholder="500.000"
                        oninput="syncPrice('min', this.value)"
                        class="flex-1 min-w-0 py-2.5 text-center text-sm font-semibold bg-transparent text-slate-900 dark:text-white placeholder-slate-400 outline-none">
                    <button type="button" onclick="stepPrice('min', 1)"
                        class="px-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-600 hover:text-amber-600 transition-colors text-lg font-bold select-none">+</button>
                </div>
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-1 text-center leading-none" id="min_price_label"></p>
            </div>

            {{-- ── FIX 2 & 3: Giá tối đa — hiển thị formatted, custom stepper ── --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-2">
                    Giá tối đa
                </label>
                <div class="flex rounded-lg border-2 border-slate-300 dark:border-slate-600 overflow-hidden focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-amber-500 transition-all bg-white dark:bg-slate-700">
                    <button type="button" onclick="stepPrice('max', -1)"
                        class="px-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-600 hover:text-amber-600 transition-colors text-lg font-bold select-none">−</button>
                    <input type="text" id="max_price_display"
                        value="{{ $maxPrice ? number_format($maxPrice, 0, ',', '.') : '' }}"
                        placeholder="10.000.000"
                        oninput="syncPrice('max', this.value)"
                        class="flex-1 min-w-0 py-2.5 text-center text-sm font-semibold bg-transparent text-slate-900 dark:text-white placeholder-slate-400 outline-none">
                    <button type="button" onclick="stepPrice('max', 1)"
                        class="px-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-600 hover:text-amber-600 transition-colors text-lg font-bold select-none">+</button>
                </div>
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-1 text-center leading-none" id="max_price_label"></p>
            </div>

            {{-- Submit Button — mt-auto đẩy nút xuống đáy, align với input ──
                 Wrapper dùng flex col để label+input+sublabel có height đồng đều --}}
            <div class="flex flex-col">
                <label class="block text-xs font-black uppercase tracking-wider text-transparent mb-2 select-none">_</label>
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-2.5 px-4 rounded-lg transition-all duration-200 active:scale-95 uppercase text-sm tracking-wide shadow-md shadow-amber-600/20">
                    🔍 Lọc
                </button>
            </div>
        </div>

        {{-- Active filters display + reset --}}
        @if($type || $minPrice || $maxPrice || $capacity)
        <div class="flex items-center justify-between text-sm">
            <div class="flex gap-2 flex-wrap">
                @if($type)
                <span class="inline-flex items-center gap-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-900 dark:text-amber-300 px-4 py-1.5 rounded-full text-sm font-bold">
                    {{ ['standard' => 'Tiêu Chuẩn', 'suite' => 'Cao Cấp', 'villa' => 'Villa', 'bungalow' => 'Gia Đình'][$type] }}
                </span>
                @endif
                @if($minPrice)
                <span class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-900 dark:text-blue-300 px-4 py-1.5 rounded-full text-sm font-bold">
                    ≥ {{ number_format($minPrice, 0, ',', '.') }}đ
                </span>
                @endif
                @if($maxPrice)
                <span class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-900 dark:text-blue-300 px-4 py-1.5 rounded-full text-sm font-bold">
                    ≤ {{ number_format($maxPrice, 0, ',', '.') }}đ
                </span>
                @endif
                @if($capacity)
                <span class="inline-flex items-center gap-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-900 dark:text-purple-300 px-4 py-1.5 rounded-full text-sm font-bold">
                    {{ $capacity === '4' ? '4 người (Gia đình)' : $capacity . ' người' }}
                </span>
                @endif
            </div>
            <a href="{{ route('rooms.index') }}"
               class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-red-50 dark:bg-slate-700 dark:hover:bg-red-900/30 text-slate-700 hover:text-red-600 dark:text-slate-300 dark:hover:text-red-400 text-sm font-bold px-4 py-1.5 rounded-lg transition-all duration-200 border border-slate-200 dark:border-slate-600 hover:border-red-200 dark:hover:border-red-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Xóa bộ lọc
            </a>
        </div>
        @endif
    </form>
</section>

{{-- ── Room grid ── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-[60vh]">

    {{-- Skeleton loading — hiện khi JS chưa xong (progressive) --}}
    <div id="skeletonGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12">
        @for($i = 0; $i < 6; $i++)
        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="skeleton h-56 w-full"></div>
            <div class="p-5 space-y-3">
                <div class="skeleton h-6 w-2/3"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-4/5"></div>
                <div class="flex justify-between items-center pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                    <div class="skeleton h-6 w-24"></div>
                    <div class="skeleton h-9 w-20 rounded-lg"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Actual room cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12">
        @forelse($rooms as $room)
        <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 flex flex-col group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

            {{-- Image --}}
            <div class="h-56 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80" alt="Phòng {{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="absolute top-3 right-3 bg-white/92 dark:bg-slate-900/92 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] font-bold text-amber-600 uppercase tracking-widest shadow-sm">
                    {{ ['standard' => 'Tiêu Chuẩn', 'suite' => 'Cao Cấp', 'villa' => 'Villa', 'bungalow' => 'Gia Đình'][$room->type] ?? $room->type }}
                </span>
                @if($room->status === 'available')
                <span class="absolute top-3 left-3 bg-emerald-500 text-white px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest">Còn phòng</span>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="text-xl font-serif font-bold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 transition-colors duration-200">Phòng {{ $room->room_number }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-2">{{ $room->view }} @if($room->zone) &middot; {{ $room->zone->name }} @endif</p>
                <div class="text-xs text-slate-400 mb-4">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $room->capacity_adults ?? 2 }} người
                    </span>
                </div>
                <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Giá / Đêm</p>
                        <p class="text-xl font-bold text-orange-600 mt-0.5">{{ number_format($room->price, 0, ',', '.') }}<span class="text-sm font-normal text-slate-400 ml-0.5">đ</span></p>
                    </div>
                    <a href="{{ route('rooms.show', $room->id) }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-amber-600 dark:hover:bg-amber-500 text-xs font-bold px-4 py-2.5 rounded-lg shadow transition-all duration-200 active:scale-95 uppercase tracking-wide">Chi tiết</a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full text-center py-24 text-slate-400 font-serif text-xl">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            @if($checkIn && $checkOut)
                Không có phòng trống trong ngày đã chọn. Vui lòng thử ngày khác.
                <p class="text-sm mt-2"><a href="{{ route('home') }}" class="text-amber-600 hover:underline">← Chọn ngày mới</a></p>
            @else
                Hệ thống đang cập nhật phòng trống…
                <p class="text-sm mt-2"><a href="{{ route('home') }}" class="text-amber-600 hover:underline">← Về trang chủ</a></p>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($rooms->hasPages())
    <div class="flex justify-center mt-4">
        {{ $rooms->links() }}
    </div>
    @endif

</section>

{{-- ── PRICE INPUT JAVASCRIPT ── --}}
<script>
/**
 * Các mức giá bước nhảy (step tiers) — hợp lý hơn step=100000 cố định:
 * < 1.000.000    → bước 500.000
 * 1M – 5M        → bước 500.000
 * 5M – 10M       → bước 1.000.000
 * > 10M          → bước 1.000.000
 */
const PRICE_MIN = 0;
const PRICE_MAX = 50000000; // 50 triệu ceiling

function getStep(value) {
    if (value < 1000000) return 500000;
    if (value < 5000000) return 500000;
    return 1000000;
}

/** Format số thành chuỗi có dấu chấm (kiểu VN): 1500000 → "1.500.000" */
function formatDisplay(num) {
    if (!num && num !== 0) return '';
    return num.toLocaleString('vi-VN');
}

/** Chuyển số thành chữ VNĐ ngắn gọn: 1500000 → "1,5 triệu" */
function toWords(num) {
    if (!num || num <= 0) return '';
    if (num >= 1000000) {
        const tr = num / 1000000;
        return (tr % 1 === 0 ? tr : tr.toFixed(1)) + ' triệu đồng';
    }
    if (num >= 1000) {
        const ng = num / 1000;
        return (ng % 1 === 0 ? ng : ng.toFixed(0)) + ' nghìn đồng';
    }
    return num.toLocaleString('vi-VN') + ' đồng';
}

/** Lấy giá trị số hiện tại từ hidden input */
function getRawValue(type) {
    const hidden = document.getElementById(type + '_price_hidden');
    return parseInt(hidden.value) || 0;
}

/** Cập nhật cả display input, hidden input, và label chữ */
function updatePrice(type, rawValue) {
    rawValue = Math.max(PRICE_MIN, Math.min(PRICE_MAX, rawValue));
    document.getElementById(type + '_price_hidden').value = rawValue || '';
    document.getElementById(type + '_price_display').value = rawValue ? formatDisplay(rawValue) : '';
    const label = document.getElementById(type + '_price_label');
    label.textContent = rawValue ? toWords(rawValue) : '';
}

/** Nút + / − bấm vào */
function stepPrice(type, direction) {
    let current = getRawValue(type);
    const step = getStep(current);
    updatePrice(type, current + direction * step);
}

/** User gõ tay vào display input → parse và sync */
function syncPrice(type, displayVal) {
    // Xóa mọi ký tự không phải số
    const raw = parseInt(displayVal.replace(/\D/g, '')) || 0;
    document.getElementById(type + '_price_hidden').value = raw || '';
    const label = document.getElementById(type + '_price_label');
    label.textContent = raw ? toWords(raw) : '';
}

/** Khi user blur khỏi input → tự format lại cho đẹp */
document.addEventListener('DOMContentLoaded', function () {
    // Gắn submit handler SAU khi DOM + JS đã load xong
    const form = document.getElementById('filterForm');
    if (form) {
        form.addEventListener('submit', submitFilter);
    }

    ['min', 'max'].forEach(function(type) {
        const display = document.getElementById(type + '_price_display');
        if (!display) return;

        display.addEventListener('blur', function () {
            const raw = parseInt(this.value.replace(/\D/g, '')) || 0;
            this.value = raw ? formatDisplay(raw) : '';
            document.getElementById(type + '_price_hidden').value = raw || '';
            checkPriceOrder(); // kiểm tra ngay khi blur
        });

        // Auto-format khi load lại trang (có giá trị cũ từ filter)
        const hidden = document.getElementById(type + '_price_hidden');
        if (hidden && hidden.value) {
            const raw = parseInt(hidden.value);
            if (raw) {
                display.value = formatDisplay(raw);
                const label = document.getElementById(type + '_price_label');
                if (label) label.textContent = toWords(raw);
            }
        }
    });
});

/**
 * Kiểm tra min <= max, hiện cảnh báo đỏ nếu sai.
 * Không block user, chỉ cảnh báo trực quan.
 */
function checkPriceOrder() {
    const minVal = getRawValue('min');
    const maxVal = getRawValue('max');
    const minDisplay = document.getElementById('min_price_display');
    const maxDisplay = document.getElementById('max_price_display');
    const minWrapper = minDisplay ? minDisplay.closest('.flex.rounded-lg') : null;
    const maxWrapper = maxDisplay ? maxDisplay.closest('.flex.rounded-lg') : null;

    const isInvalid = minVal > 0 && maxVal > 0 && minVal > maxVal;

    [minWrapper, maxWrapper].forEach(function(el) {
        if (!el) return;
        if (isInvalid) {
            el.classList.add('border-red-500');
            el.classList.remove('border-slate-300', 'dark:border-slate-600');
        } else {
            el.classList.remove('border-red-500');
            el.classList.add('border-slate-300');
        }
    });

    // Hiện/ẩn warning text
    let warn = document.getElementById('price_order_warning');
    if (isInvalid) {
        if (!warn) {
            warn = document.createElement('p');
            warn.id = 'price_order_warning';
            warn.className = 'text-red-500 dark:text-red-400 text-xs font-semibold mt-2 col-span-full';
            warn.innerHTML = '⚠️ Giá tối thiểu không được lớn hơn giá tối đa. Sẽ tự động đổi chỗ khi lọc.';
            const grid = document.querySelector('#filterForm .grid');
            if (grid) grid.insertAdjacentElement('afterend', warn);
        }
        warn.style.display = 'block';
    } else if (warn) {
        warn.style.display = 'none';
    }

    return isInvalid;
}

/**
 * Validate + build URL sạch rồi navigate — không để số thô lên URL.
 * Chỉ append param nào user thực sự chọn (khác rỗng).
 */
function submitFilter(e) {
    e.preventDefault();

    let minVal = getRawValue('min');
    let maxVal = getRawValue('max');

    // Swap nếu ngược
    if (minVal > 0 && maxVal > 0 && minVal > maxVal) {
        [minVal, maxVal] = [maxVal, minVal];
    }

    const params = new URLSearchParams();

    // Date không đưa lên URL — đọc từ session phía server
    // Filters — chỉ append nếu có giá trị
    const type     = document.querySelector('select[name="type"]').value;
    const capacity = document.querySelector('select[name="capacity"]').value;
    if (type)     params.set('type',      type);
    if (minVal)   params.set('min_price', minVal);
    if (maxVal)   params.set('max_price', maxVal);
    if (capacity) params.set('capacity',  capacity);

    // Navigate với URL sạch
    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    return false;
}
</script>

@endsection