@extends('layouts.frontend')

@section('title', 'Phòng ' . $room->room_number . ' | Resort Pro')
@section('has_hero', 'true')

@section('content')

@php
    $typeLabels = ['standard' => 'Tiêu Chuẩn', 'suite' => 'Phòng Cao Cấp', 'villa' => 'Villa Nguyên Căn', 'bungalow' => 'Phòng Gia Đình'];
    $statusMap = [
        'available'   => ['label' => 'Còn phòng',  'color' => '#10b981'],
        'booked'      => ['label' => 'Đã đặt',      'color' => '#f43f5e'],
        'maintenance' => ['label' => 'Bảo trì',     'color' => '#64748b'],
    ];
    $amenities = [
        ['icon' => 'M5 12.55a11 11 0 0114.08 0M1.42 9a16 16 0 0121.16 0M8.53 16.11a6 6 0 016.95 0M12 20h.01', 'label' => 'WiFi tốc độ cao'],
        ['icon' => 'M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1014 16H2m15.73-8.27A2 2 0 1119 12H2', 'label' => 'Điều hòa nhiệt độ'],
        ['icon' => 'M9 7h6l-1 7H10L9 7zM6 7l.5-3h11L18 7M8 17h8m-4-3v3', 'label' => 'Minibar & Đồ uống'],
        ['icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Smart TV 55"'],
        ['icon' => 'M4 12h16M4 12a4 4 0 01-1-8 4 4 0 011 8zm0 0v4a2 2 0 002 2h12a2 2 0 002-2v-4M9 7v1m6-1v1', 'label' => 'Bồn tắm riêng'],
        ['icon' => 'M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v9a2 2 0 01-2-2h-2M14 17a2 2 0 11-4 0 2 2 0 014 0zM7 17a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Đưa đón sân bay'],
        ['icon' => 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2zM9 22V12h6v10', 'label' => 'Ban công riêng'],
        ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Dịch vụ phòng 24/7'],
    ];
    $gallery = [
        'https://images.pexels.com/photos/1457842/pexels-photo-1457842.jpeg?auto=compress&w=600',
        'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&w=600',
        'https://images.pexels.com/photos/271618/pexels-photo-271618.jpeg?auto=compress&w=600',
        'https://images.pexels.com/photos/237371/pexels-photo-237371.jpeg?auto=compress&w=600',
        'https://images.pexels.com/photos/276671/pexels-photo-276671.jpeg?auto=compress&w=600',
        'https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&w=600',
    ];
@endphp

{{-- HERO --}}
<section class="relative w-full h-[70vh] min-h-[500px] overflow-hidden bg-slate-900">
    <img id="heroImg" src="https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg?auto=compress&cs=tinysrgb&w=1920" class="absolute inset-0 w-full h-full object-cover will-change-transform" style="transform:scale(1.08)" alt="Phòng {{ $room->room_number }}">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/30 to-transparent"></div>
    <div class="absolute inset-x-0 bottom-0 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 z-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="inline-block mb-3 bg-amber-600 text-white text-[10px] font-bold uppercase tracking-[0.2em] px-3 py-1 rounded">{{ $typeLabels[$room->type] ?? $room->type }}</span>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-serif text-white leading-none drop-shadow-lg">Phòng {{ $room->room_number }}</h1>
                <p class="text-slate-300 mt-2 font-light text-lg">
                    {{ $room->zone->name ?? 'Khu vực chung' }}
                    @if(isset($statusMap[$room->status]))
                        &nbsp;&middot;&nbsp;
                        <span style="color:{{ $statusMap[$room->status]['color'] }}" class="font-semibold text-sm">{{ $statusMap[$room->status]['label'] }}</span>
                    @endif
                </p>
            </div>
            <div class="hidden sm:flex items-baseline gap-2 bg-black/40 backdrop-blur-md border border-white/15 px-5 py-3 rounded-2xl">
                <span class="text-2xl font-serif font-bold text-amber-400">{{ number_format($room->price) }}đ</span>
                <span class="text-slate-400 text-sm">/đêm</span>
            </div>
        </div>
    </div>
</section>

<style>
    .room-detail-bg { background-color: #f9f8f6; }
    html.dark .room-detail-bg { background-color: transparent; }
    .room-detail-bg, .room-detail-bg * { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    .label-sm { font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
    .section-label { font-size: 12px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #d97706; margin-bottom: 1rem; }
    html.dark .section-label { color: #f59e0b; }
    .info-card { background: #ffffff; border: 1.5px solid #e4e2de; border-radius: 1rem; padding: 1.5rem; }
    html.dark .info-card { background: #1e293b; border-color: #334155; }
    html.dark  .date-input { color-scheme: dark; }
    html.light .date-input { color-scheme: light; }
    .date-input::-webkit-calendar-picker-indicator { cursor: pointer; padding: 2px; border-radius: 4px; }
    html.light .date-input::-webkit-calendar-picker-indicator { opacity: 0.7; }
    html.light .date-input::-webkit-calendar-picker-indicator:hover { opacity: 1; background: rgba(0,0,0,0.06); }
    html.dark .date-input::-webkit-calendar-picker-indicator { filter: invert(0.8) brightness(1.2); }
    html.dark .date-input::-webkit-calendar-picker-indicator:hover { background: rgba(255,255,255,0.1); }
</style>

<div class="room-detail-bg min-h-screen transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-10 xl:gap-16">

            {{-- ══ LEFT: CONTENT ══ --}}
            <div class="lg:w-[60%] space-y-12">
                <div>
                    <p class="section-label">Tổng quan</p>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-lg">Trải nghiệm không gian nghỉ dưỡng tuyệt vời tại Phòng {{ $room->room_number }}. Với thiết kế sang trọng, hiện đại pha lẫn nét cổ điển, căn phòng mang đến cảm giác ấm cúng và thư giãn tuyệt đối. {{ $room->view }}.</p>
                </div>

                <div>
                    <p class="section-label">Thông tin phòng</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="info-card flex flex-col gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <p class="label-sm text-slate-400">Sức chứa</p>
                            <p class="font-bold text-slate-900 dark:text-white text-2xl">{{ $room->capacity_adults ?? 2 }} người</p>
                        </div>
                        <div class="info-card flex flex-col gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            <p class="label-sm text-slate-400">Giá / đêm</p>
                            <p class="font-bold text-amber-600 dark:text-amber-400 text-2xl">{{ number_format($room->price) }}đ</p>
                        </div>
                        <div class="info-card flex flex-col gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="label-sm text-slate-400">Hướng nhìn</p>
                            <p class="font-bold text-slate-900 dark:text-white text-base leading-snug">{{ $room->view }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="section-label">Hình ảnh thực tế</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($gallery as $gImg)
                        <div class="aspect-[4/3] overflow-hidden rounded-xl group cursor-zoom-in">
                            <img src="{{ $gImg }}" alt="Phòng {{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="section-label">Tiện nghi đầy đủ</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($amenities as $amenity)
                        <div class="info-card flex flex-col items-center text-center gap-3 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-all duration-200 group cursor-default">
                            <svg class="w-7 h-7 text-amber-500 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $amenity['icon'] }}"/></svg>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 leading-snug">{{ $amenity['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div id="checkinCard" class="info-card transition-all duration-300">
                        <div id="checkinPlaceholder">
                            <p class="label-sm text-slate-400 mb-3">Lịch của bạn</p>
                            <div class="flex flex-col items-center justify-center py-6 gap-3 text-slate-400 dark:text-slate-500">
                                <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-center leading-relaxed text-slate-500">Chọn ngày nhận &amp; trả phòng<br>để xem lịch chi tiết</p>
                            </div>
                        </div>

                        <div id="checkinFilled" class="hidden space-y-3">
                            <p class="label-sm text-amber-600 dark:text-amber-400 mb-1">Lịch của bạn</p>
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/15 border border-emerald-200 dark:border-emerald-800/40">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <div>
                                    <p class="label-sm text-emerald-600 dark:text-emerald-400">Nhận phòng</p>
                                    <p id="checkinDateDisplay" class="font-bold text-slate-800 dark:text-slate-100 text-base mt-1"></p>
                                    <p class="text-sm text-slate-500 mt-0.5">Từ 14:00 — cần thời gian dọn phòng</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-rose-50 dark:bg-rose-900/15 border border-rose-200 dark:border-rose-800/40">
                                <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <div>
                                    <p class="label-sm text-rose-500 dark:text-rose-400">Trả phòng</p>
                                    <p id="checkoutDateDisplay" class="font-bold text-slate-800 dark:text-slate-100 text-base mt-1"></p>
                                    <p class="text-sm text-slate-500 mt-0.5">Trước 12:00 — để kịp dọn cho khách</p>
                                </div>
                            </div>
                            <div class="flex justify-center pt-1">
                                <span id="durationBadge" class="text-sm font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 px-4 py-1.5 rounded-full"></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/40 dark:bg-amber-900/10 transition-colors">
                        <p class="label-sm text-amber-600 dark:text-amber-400 mb-4">Chính sách đặt &amp; hủy</p>
                        <div class="space-y-3 text-sm text-slate-700 dark:text-slate-300">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span><strong class="text-slate-800 dark:text-slate-200">Đặt cọc 30%</strong> khi xác nhận để giữ phòng</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Hủy miễn phí trong <strong class="text-slate-800 dark:text-slate-200">24 giờ đầu</strong> sau khi đặt</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span>Hủy sau 24 giờ hoặc trước <strong class="text-slate-800 dark:text-slate-200">7 ngày nhận phòng</strong> — mất cọc</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Không hoàn tiền nếu hủy <strong class="text-slate-800 dark:text-slate-200">dưới 48 giờ</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT: Booking Widget ══ --}}
            <div class="lg:w-[40%]">
                <div class="sticky top-24">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 backdrop-blur-sm shadow-sm dark:shadow-none transition-colors duration-300">
                        <div class="px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-700/60">
                            <p class="label-sm text-slate-400 mb-1">Đặt phòng</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-serif font-bold text-slate-900 dark:text-white">{{ number_format($room->price) }}đ</span>
                                <span class="text-base text-slate-500">/đêm</span>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-4">
                            <form action="{{ route('booking.check') }}" method="POST">
                                @csrf
                                <input type="hidden" name="preferred_room_id" value="{{ $room->id }}">

                                <div class="border border-slate-300 dark:border-slate-600 rounded-xl focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20 transition-all duration-200">
                                    <div class="grid grid-cols-2 divide-x divide-slate-200 dark:divide-slate-600/80">
                                        <label for="check_in_d" class="block px-4 py-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-l-xl select-none">
                                            <span class="block text-[11px] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400 mb-2">Nhận phòng</span>
                                            <input type="date" name="check_in" id="check_in_d" required class="date-input w-full bg-transparent text-slate-800 dark:text-slate-100 text-sm font-semibold border-0 outline-none focus:ring-0 cursor-pointer pointer-events-none">
                                        </label>
                                        <label for="check_out_d" class="block px-4 py-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-r-xl select-none">
                                            <span class="block text-[11px] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400 mb-2">Trả phòng</span>
                                            <input type="date" name="check_out" id="check_out_d" required class="date-input w-full bg-transparent text-slate-800 dark:text-slate-100 text-sm font-semibold border-0 outline-none focus:ring-0 cursor-pointer pointer-events-none">
                                        </label>
                                    </div>

                                    <div id="nightsSummary" class="hidden border-t border-slate-200 dark:border-slate-600/80 px-4 py-3 bg-amber-50 dark:bg-amber-900/20">
                                        <div class="flex justify-between items-center">
                                            <span id="nightsText" class="text-sm font-semibold text-amber-700 dark:text-amber-400"></span>
                                            <span id="totalPriceText" class="text-sm font-bold text-slate-800 dark:text-slate-100"></span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="mt-4 w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-4 px-6 rounded-xl text-sm uppercase tracking-widest shadow-md shadow-amber-600/15 hover:shadow-amber-500/25 transition-all duration-200 active:scale-[.98]">
                                    Kiểm tra lịch trống →
                                </button>
                            </form>

                            <div class="space-y-2 pt-1">
                                @foreach(['Miễn phí hủy trong 48 giờ đầu' => 'M5 13l4 4L19 7', 'Xác nhận tức thì qua email' => 'M5 13l4 4L19 7', 'Không thu phí ẩn' => 'M5 13l4 4L19 7'] as $trustText => $icon)
                                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                    {{ $trustText }}
                                </div>
                                @endforeach
                            </div>
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-700/50 text-center">
                                <p class="text-sm text-slate-500">Cần tư vấn? <a href="tel:+84123456789" class="text-amber-600 hover:text-amber-500 font-semibold">Gọi hotline ngay</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-3 text-sm text-slate-400 dark:text-slate-500">
                        <div class="flex">
                            @for($i=0;$i<5;$i++)
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span>4.9 · 128 đánh giá</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var img = document.getElementById('heroImg');
    if(img && !window.matchMedia('(prefers-reduced-motion:reduce)').matches){
        window.addEventListener('scroll',function(){ img.style.transform='scale(1.08) translateY('+(window.scrollY*0.18)+'px)'; },{passive:true});
    }

    var ROOM_ID  = '{{ $room->id }}';
    var SS_KEY   = 'room_dates_' + ROOM_ID;
    var price    = {{ $room->price }};
    var ci       = document.getElementById('check_in_d');
    var co       = document.getElementById('check_out_d');
    var summary  = document.getElementById('nightsSummary');
    var nText    = document.getElementById('nightsText');
    var pText    = document.getElementById('totalPriceText');

    var placeholder     = document.getElementById('checkinPlaceholder');
    var filled          = document.getElementById('checkinFilled');
    var checkinDisplay  = document.getElementById('checkinDateDisplay');
    var checkoutDisplay = document.getElementById('checkoutDateDisplay');
    var durationBadge   = document.getElementById('durationBadge');

    var DAYS_VI   = ['Chủ nhật','Thứ hai','Thứ ba','Thứ tư','Thứ năm','Thứ sáu','Thứ bảy'];
    var MONTHS_VI = ['tháng 1','tháng 2','tháng 3','tháng 4','tháng 5','tháng 6', 'tháng 7','tháng 8','tháng 9','tháng 10','tháng 11','tháng 12'];

    function fmtFull(iso){
        var d = new Date(iso+'T00:00:00');
        return DAYS_VI[d.getDay()]+', '+d.getDate()+' '+MONTHS_VI[d.getMonth()]+' '+d.getFullYear();
    }
    function fmtShort(iso){
        var p = iso.split('-'); return p[2]+'/'+p[1];
    }
    function todayISO(){
        var d = new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
    }

    function saveDates(){
        try {
            sessionStorage.setItem(SS_KEY, JSON.stringify({ check_in: ci.value || '', check_out: co.value || '' }));
        } catch(e){}
    }

    function restoreDates(){
        try {
            var saved = sessionStorage.getItem(SS_KEY);
            if(!saved) return;
            var data = JSON.parse(saved);
            var today = todayISO();
            if(data.check_in && data.check_in >= today) ci.value = data.check_in;
            if(data.check_out && data.check_out > (ci.value || today)) co.value = data.check_out;
            sessionStorage.removeItem(SS_KEY);
            refreshPreview();
        } catch(e){}
    }

    document.querySelectorAll('a[href*="login"]').forEach(function(link){
        link.addEventListener('click', saveDates);
    });

    function refreshPreview(){
        if(!ci||!co) return;
        var hasRange = ci.value && co.value && co.value > ci.value;

        if(hasRange){
            var n = Math.round((new Date(co.value)-new Date(ci.value))/864e5);
            nText.textContent = n+' đêm  ·  '+fmtShort(ci.value)+' → '+fmtShort(co.value);
            pText.textContent = (n*price).toLocaleString('vi-VN')+'đ';
            summary.classList.remove('hidden');

            checkinDisplay.textContent  = fmtFull(ci.value);
            checkoutDisplay.textContent = fmtFull(co.value);
            durationBadge.textContent   = n+' đêm · '+(n+1)+' ngày';
            placeholder.classList.add('hidden');
            filled.classList.remove('hidden');
        } else {
            summary.classList.add('hidden');
            placeholder.classList.remove('hidden');
            filled.classList.add('hidden');
        }
    }

    if(ci) ci.addEventListener('change', function(){ saveDates(); refreshPreview(); });
    if(co) co.addEventListener('change', function(){ saveDates(); refreshPreview(); });

    [
        {label: document.querySelector('label[for="check_in_d"]'),  input: ci},
        {label: document.querySelector('label[for="check_out_d"]'), input: co},
    ].forEach(function(pair){
        if(!pair.label || !pair.input) return;
        pair.label.addEventListener('click', function(){
            pair.input.focus();
            try { pair.input.showPicker(); } catch(e){}
        });
    });

    restoreDates();
})();
</script>

@endsection