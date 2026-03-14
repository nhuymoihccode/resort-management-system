@extends('layouts.frontend')

@section('title', 'Danh sách Phòng & Villa | Resort Pro')
@section('has_hero', 'false')

@section('content')

{{-- ── Page header ── --}}
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 pt-24 pb-10 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-amber-600 font-semibold tracking-[0.2em] uppercase text-xs mb-3">Không gian lưu trú</p>
        <h1 class="text-4xl md:text-5xl font-serif text-slate-900 dark:text-white mb-3">
            Khám phá không gian lưu trú
        </h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto text-sm leading-relaxed">
            Lựa chọn hoàn hảo cho kỳ nghỉ dưỡng của bạn với các tiện nghi đẳng cấp và tầm nhìn tuyệt mỹ.
        </p>
    </div>
</div>

{{-- ── Filter bar (mobile-friendly) ── --}}
@php
    $typeLabels = [
        'standard' => 'Tiêu Chuẩn',
        'suite'    => 'Phòng Cao Cấp',
        'villa'    => 'Villa Nguyên Căn',
        'bungalow' => 'Gia Đình',
    ];
@endphp

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
                <span class="absolute top-3 right-3 bg-white/92 dark:bg-slate-900/92 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] font-bold text-amber-600 uppercase tracking-widest shadow-sm">{{ $typeLabels[$room->type] ?? $room->type }}</span>
                @if($room->status === 'available')
                <span class="absolute top-3 left-3 bg-emerald-500 text-white px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest">Còn phòng</span>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="text-xl font-serif font-bold text-slate-900 dark:text-white mb-1 group-hover:text-amber-600 transition-colors duration-200">Phòng {{ $room->room_number }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">{{ $room->view }} @if($room->zone) &middot; {{ $room->zone->name }} @endif</p>
                <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Giá / Đêm</p>
                        <p class="text-xl font-bold text-orange-600 mt-0.5">{{ number_format($room->price) }}<span class="text-sm font-normal text-slate-400 ml-0.5">đ</span></p>
                    </div>
                    <a href="{{ route('rooms.show', $room->id) }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-amber-600 dark:hover:bg-amber-500 text-xs font-bold px-4 py-2.5 rounded-lg shadow transition-all duration-200 active:scale-95 uppercase tracking-wide">Chi tiết</a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full text-center py-24 text-slate-400 font-serif text-xl">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Hệ thống đang cập nhật phòng trống…
            <p class="text-sm mt-2"><a href="{{ route('home') }}" class="text-amber-600 hover:underline">← Về trang chủ</a></p>
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

@endsection