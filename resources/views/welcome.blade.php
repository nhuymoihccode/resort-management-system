@extends('layouts.frontend')

@section('title', 'Resort Pro | Tôn vinh đẳng cấp nghỉ dưỡng')
@section('has_hero', 'true')

@section('content')

{{-- HERO --}}
<section class="relative w-full h-screen min-h-[620px] flex items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0">
        <img id="heroImg" src="https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&cs=tinysrgb&w=1920" class="w-full h-full object-cover will-change-transform" style="transform: scale(1.08)" alt="Resort">
        <div class="absolute inset-0 bg-gradient-to-b from-black/25 via-black/40 to-black/75"></div>
    </div>
    <div class="relative z-10 text-center px-4 max-w-4xl">
        <p class="text-amber-400 font-semibold tracking-[0.3em] uppercase text-xs mb-6 opacity-0 animate-[fadeUp_0.8s_0.2s_ease_forwards]">✦ Kỳ nghỉ đẳng cấp ✦</p>
        <h1 class="text-5xl sm:text-6xl md:text-7xl font-serif text-white leading-[1.1] drop-shadow-2xl opacity-0 animate-[fadeUp_0.8s_0.4s_ease_forwards]">Nơi thời gian<br><em class="text-amber-400 not-italic">ngừng trôi.</em></h1>
        <p class="text-slate-300 mt-5 text-lg max-w-xl mx-auto font-light opacity-0 animate-[fadeUp_0.8s_0.6s_ease_forwards]">Trải nghiệm không gian nghỉ dưỡng sang trọng, nơi thiên nhiên và đẳng cấp hòa quyện.</p>
        <a href="#search-bar" class="inline-block mt-8 bg-amber-500 hover:bg-amber-400 text-white font-bold px-8 py-4 rounded-lg shadow-xl shadow-amber-900/30 transition-all duration-300 active:scale-95 uppercase tracking-widest text-sm opacity-0 animate-[fadeUp_0.8s_0.8s_ease_forwards]">Đặt phòng ngay</a>
    </div>
    <a href="#search-bar" class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-50 hover:opacity-80 transition-opacity">
        <span class="text-[10px] text-white uppercase tracking-[0.2em]">Cuộn xuống</span>
        <svg class="w-5 h-5 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </a>
</section>

{{-- SEARCH BAR --}}
<section id="search-bar" class="relative z-20 max-w-4xl mx-auto px-4 sm:px-6 -mt-16 mb-20">
    <form action="{{ route('rooms.search') }}" method="POST" class="bg-white dark:bg-slate-800 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.18)] border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row overflow-hidden transition-colors duration-300">
        @csrf
        <div class="flex-1 px-6 py-5 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-700">
            <label for="check_in" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nhận phòng</label>
            <input id="check_in" type="date" name="check_in" required class="w-full bg-transparent border-0 p-0 text-slate-800 dark:text-white font-semibold text-base focus:ring-0 outline-none cursor-pointer">
        </div>
        <div class="flex-1 px-6 py-5 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-700">
            <label for="check_out" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Trả phòng</label>
            <input id="check_out" type="date" name="check_out" required class="w-full bg-transparent border-0 p-0 text-slate-800 dark:text-white font-semibold text-base focus:ring-0 outline-none cursor-pointer">
        </div>
        {{-- ĐÃ ĐỔI ID ĐỂ TRÁNH XUNG ĐỘT VỚI TRANG DETAIL --}}
        <div class="flex items-center justify-center px-4 py-2 sm:py-0 sm:min-w-[90px]">
            <span id="nightsPreviewWelcome"
                  class="hidden text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/30
                         border border-amber-200 dark:border-amber-700 px-3 py-1.5 rounded-full whitespace-nowrap">
            </span>
        </div>

        <div class="px-4 py-4 flex items-center justify-center sm:min-w-[130px]">
            <button type="submit"
                    class="w-full sm:w-auto bg-amber-600 hover:bg-amber-500 active:scale-95 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 uppercase text-sm tracking-wide shadow-lg shadow-amber-600/20">
                Tìm phòng
            </button>
        </div>
    </form>
</section>

{{-- FEATURED ROOMS --}}
<section id="featured-rooms" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="flex flex-col sm:flex-row justify-between items-end mb-12">
        <div>
            <p class="text-amber-600 font-semibold tracking-[0.2em] uppercase text-xs mb-3">Không gian lưu trú</p>
            <h2 class="text-3xl sm:text-4xl font-serif text-slate-900 dark:text-white">Tuyệt tác Kiến trúc</h2>
        </div>
        <a href="{{ route('rooms.index') }}" class="mt-4 sm:mt-0 text-sm font-bold tracking-wider uppercase text-amber-600 hover:text-amber-700 border-b-2 border-amber-600 hover:border-amber-700 pb-1 transition-colors">Xem toàn bộ →</a>
    </div>

    @php
        $typeLabels = [
            'standard' => 'Tiêu Chuẩn',
            'suite'    => 'Phòng Cao Cấp',
            'villa'    => 'Villa',
            'bungalow' => 'Gia Đình',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @forelse($featuredRooms as $room)
        <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 flex flex-col group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
            <div class="h-56 bg-slate-200 relative overflow-hidden">
                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80" alt="Phòng {{ $room->room_number }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="absolute top-3 right-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] font-bold text-amber-600 uppercase tracking-widest shadow-sm">{{ $typeLabels[$room->type] ?? $room->type }}</span>
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="text-xl font-serif font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-amber-600 transition-colors">Phòng {{ $room->room_number }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-5">{{ $room->view }} @if($room->zone) · {{ $room->zone->name }} @endif</p>
                <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Giá / Đêm</p>
                        <p class="text-lg font-bold text-orange-600 mt-0.5">{{ number_format($room->price) }}<span class="text-sm text-slate-400 font-normal ml-0.5">đ</span></p>
                    </div>
                    <a href="{{ route('rooms.show', $room->id) }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-amber-600 dark:hover:bg-amber-500 text-xs font-bold px-4 py-2.5 rounded-lg shadow transition-all duration-200 active:scale-95 uppercase tracking-wide">Chi tiết</a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full text-center py-20 text-slate-400 font-serif text-xl">Đang cập nhật phòng trống…</div>
        @endforelse
    </div>
</section>

{{-- SERVICES --}}
<section class="section-services w-full py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-14">
            <p class="text-amber-500 font-semibold tracking-[0.2em] uppercase text-xs mb-3">Trải nghiệm</p>
            <h2 class="text-3xl sm:text-4xl font-serif section-heading-text">Dịch vụ Thượng lưu</h2>
        </div>

        @php
            $serviceImages = [
                'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1599839619722-39751411ea63?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1533472492984-601726a51187?auto=format&fit=crop&w=800&q=80',
            ];
            $unitLabels = ['person' => 'Người', 'hour' => 'Giờ', 'turn' => 'Lượt'];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach($services->take(4) as $i => $service)
            <div class="relative h-72 sm:h-80 group overflow-hidden rounded-2xl cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-110 will-change-transform" style="background-image: url('{{ $serviceImages[$i % 4] }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/10"></div>
                <div class="absolute inset-x-0 bottom-0 p-7">
                    <h3 class="font-serif text-xl text-white mb-2">{{ $service->name }}</h3>
                    <div class="flex justify-between items-end translate-y-3 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 ease-out">
                        <p class="text-amber-400 font-semibold text-base">{{ number_format($service->price) }}<span class="text-xs text-slate-400 font-normal ml-1">đ / {{ $unitLabels[$service->unit] ?? $service->unit }}</span></p>
                        <span class="text-xs uppercase tracking-widest text-white/80 border-b border-white/60 pb-0.5">Khám phá</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- WHY US / STATS --}}
<section class="section-why w-full py-20 sm:py-28">
    <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center mb-14">
            <p class="text-[11px] font-bold tracking-[0.35em] uppercase text-amber-500 mb-4">Về chúng tôi</p>
            <h2 class="font-serif text-4xl sm:text-5xl section-heading-text leading-snug">Cam kết bằng<br><em class="text-amber-400 not-italic">con số thực tế</em></h2>
            <p class="section-sub-text mt-4 text-sm sm:text-base max-w-md mx-auto leading-relaxed">Không tự phong danh hiệu. Mọi số liệu đều có thể kiểm chứng.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            @php
                $stats = [
                    ['target' => $totalRooms, 'display' => null, 'suffix' => '', 'label' => 'Phòng & Villa', 'desc' => 'Từ phòng tiêu chuẩn đến villa nguyên căn hướng biển', 'source' => 'Dữ liệu hệ thống', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['target' => $totalServices, 'display' => null, 'suffix' => '+', 'label' => 'Dịch vụ cao cấp', 'desc' => 'Spa, ẩm thực, thể thao và trải nghiệm ngoài trời', 'source' => 'Danh mục dịch vụ', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                    ['target' => null, 'display' => '4.8<span class="font-serif text-2xl text-amber-400 font-normal">/5</span>', 'suffix' => '', 'label' => 'Đánh giá khách', 'desc' => 'Tổng hợp từ Google Maps & Booking.com', 'source' => 'Khảo sát độc lập', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                    ['target' => null, 'display' => '24/7', 'suffix' => '', 'label' => 'Hỗ trợ liên tục', 'desc' => 'Lễ tân và chăm sóc khách phục vụ cả ngày lẫn đêm', 'source' => 'Cam kết vận hành', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                ];
            @endphp

            @foreach($stats as $idx => $stat)
            <div class="stat-card rounded-2xl p-7 flex flex-col items-center text-center gap-4 transition-all duration-300" style="animation-delay: {{ $idx * 80 }}ms">
                <div class="stat-icon-wrap w-16 h-16 flex items-center justify-center rounded-2xl transition-transform duration-300">
                    <svg class="w-9 h-9 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/></svg>
                </div>
                <div class="flex-1">
                    <p class="stat-number font-serif text-5xl font-bold leading-none mb-1">
                        @if($stat['target'] !== null)
                            <span class="num" data-target="{{ $stat['target'] }}">0</span>{{ $stat['suffix'] }}
                        @else
                            {!! $stat['display'] !!}
                        @endif
                    </p>
                    <p class="stat-label text-base font-semibold mt-2 mb-1.5">{{ $stat['label'] }}</p>
                    <p class="stat-desc text-xs leading-relaxed">{{ $stat['desc'] }}</p>
                </div>
                <span class="stat-source text-[10px] uppercase tracking-widest font-medium">{{ $stat['source'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
@keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

/* Light mode */
.section-services { background: #f1f5f9; } .section-why { background: #e2e8f0; } .section-heading-text { color: #1e293b; } .section-sub-text { color: #64748b; }
.stat-card { background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 2px 12px rgba(0,0,0,0.06); } .stat-card:hover { border-color: #fbbf24; box-shadow: 0 4px 20px rgba(251,191,36,0.14); }
.stat-icon-wrap { background: rgba(217,119,6,0.1); } .stat-card:hover .stat-icon-wrap { transform: scale(1.1) translateY(-2px); }
.stat-number { color: #1e293b; } .stat-label { color: #334155; } .stat-desc { color: #64748b; } .stat-source { color: #94a3b8; }

/* Dark mode */
html.dark .section-services { background: #1e293b; } html.dark .section-why { background: #0f172a; } html.dark .section-heading-text { color: #f8fafc; } html.dark .section-sub-text { color: #94a3b8; }
html.dark .stat-card { background: #1e293b; border: 1px solid rgba(255,255,255,0.06); box-shadow: none; } html.dark .stat-card:hover { border-color: rgba(251,191,36,0.3); } html.dark .stat-icon-wrap { background: rgba(245,158,11,0.12); }
html.dark .stat-number { color: #f8fafc; } html.dark .stat-label { color: #e2e8f0; } html.dark .stat-desc { color: #64748b; } html.dark .stat-source { color: #334155; }
</style>

<script>
(function () {
    var img = document.getElementById('heroImg');
    if (img && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.addEventListener('scroll', function () {
            img.style.transform = 'scale(1.08) translateY(' + (window.scrollY * 0.22) + 'px)';
        }, { passive: true });
    }

    var nums = document.querySelectorAll('.num[data-target]');
    if (nums.length && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                var el = e.target;
                var target = parseInt(el.dataset.target, 10);
                var t0 = performance.now();
                var dur = 1200;
                (function tick(now) {
                    var p = Math.min((now - t0) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(eased * target);
                    if (p < 1) requestAnimationFrame(tick);
                })(performance.now());
                io.unobserve(el);
            });
        }, { threshold: 0.4 });
        nums.forEach(function (n) { io.observe(n); });
    }
})();
</script>

@endsection