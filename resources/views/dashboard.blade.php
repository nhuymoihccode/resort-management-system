@extends('layouts.frontend')
 
@section('title', 'Dashboard | Resort Pro')
@section('has_hero', 'false')
 
@section('content')
 
<div class="bg-slate-50 dark:bg-slate-900 min-h-screen pt-24 pb-16 transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
 
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-xs font-bold tracking-[.2em] uppercase text-amber-600 dark:text-amber-400 mb-1">Tài khoản của bạn</p>
            <h1 class="text-3xl font-serif font-bold text-slate-900 dark:text-white">
                Xin chào, {{ $user->name }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
                Quản lý đặt phòng và thông tin cá nhân của bạn.
            </p>
        </div>
 
        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 transition-colors">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Tổng đặt phòng</p>
                <p class="text-3xl font-serif font-bold text-slate-900 dark:text-white">{{ $orders->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 transition-colors">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Trạng thái</p>
                <p class="text-base font-semibold text-emerald-600 dark:text-emerald-400 mt-1">Hoạt động</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 transition-colors col-span-2 sm:col-span-1">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Email</p>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate mt-1">{{ $user->email }}</p>
            </div>
        </div>
 
        {{-- Booking list --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-serif font-bold text-slate-900 dark:text-white">Lịch sử đặt phòng</h2>
                <a href="{{ route('rooms.index') }}"
                   class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-widest transition-colors">
                    Đặt phòng mới →
                </a>
            </div>
 
            @if($orders->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-slate-700 dark:text-slate-300 mb-1">Chưa có đặt phòng nào</p>
                    <p class="text-sm text-slate-400 mb-5">Khám phá các phòng và bắt đầu kỳ nghỉ của bạn!</p>
                    <a href="{{ route('rooms.index') }}"
                       class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white
                              text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                        Xem phòng trống →
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($orders as $order)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 text-sm">
                                Phòng {{ $order->room->room_number ?? '—' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($order->check_in)->format('d/m/Y') }}
                                → {{ \Carbon\Carbon::parse($order->check_out)->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $order->status === 'confirmed'
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                            {{ $order->status === 'confirmed' ? 'Đã xác nhận' : 'Chờ xử lý' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
 
        {{-- Quick links --}}
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400
                      hover:text-amber-600 dark:hover:text-amber-400 transition-colors
                      border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5
                      hover:border-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Chỉnh sửa hồ sơ
            </a>
            <a href="{{ route('rooms.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-white
                      bg-amber-600 hover:bg-amber-500 rounded-xl px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Xem phòng & Villa
            </a>
        </div>
 
    </div>
</div>
 
@endsection