@extends('layouts.frontend')

@section('title', 'Hồ sơ cá nhân | Resort Pro')
@section('has_hero', 'false')

@section('content')
<div class="bg-slate-50 dark:bg-slate-900 min-h-screen py-24 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="text-center mb-10">
            <h2 class="text-3xl font-serif font-bold text-slate-900 dark:text-white">Hồ sơ cá nhân</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Quản lý thông tin bảo mật và cài đặt tài khoản của bạn.</p>
        </div>

        {{-- Update Profile Information --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Thông tin cá nhân</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Cập nhật tên hiển thị và địa chỉ email của bạn.</p>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('patch')

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                           class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-amber-500 focus:border-amber-500 transition">
                    @error('name', 'updateProfileInformation')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-amber-500 focus:border-amber-500 transition">
                    @error('email', 'updateProfileInformation')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 text-sm text-amber-600 dark:text-amber-500">
                            Email của bạn chưa được xác minh.
                            <button form="send-verification" class="underline hover:text-amber-700 dark:hover:text-amber-400 font-semibold ml-1">
                                Bấm vào đây để gửi lại email xác minh.
                            </button>
                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-emerald-600 dark:text-emerald-500 font-medium">Link xác minh mới đã được gửi.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow">
                        Lưu thay đổi
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm font-medium text-emerald-600 dark:text-emerald-500 transition-opacity" id="status-profile">Đã lưu thành công.</p>
                        <script>setTimeout(() => document.getElementById('status-profile').style.opacity = '0', 3000);</script>
                    @endif
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Cập nhật mật khẩu</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Sử dụng mật khẩu dài và ngẫu nhiên để bảo mật tài khoản.</p>

            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('put')

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" required
                           class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-amber-500 focus:border-amber-500 transition">
                    @error('current_password', 'updatePassword')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Mật khẩu mới</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-amber-500 focus:border-amber-500 transition">
                    @error('password', 'updatePassword')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-amber-500 focus:border-amber-500 transition">
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow">
                        Đổi mật khẩu
                    </button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm font-medium text-emerald-600 dark:text-emerald-500 transition-opacity" id="status-pwd">Đã cập nhật mật khẩu.</p>
                        <script>setTimeout(() => document.getElementById('status-pwd').style.opacity = '0', 3000);</script>
                    @endif
                </div>
            </form>
        </div>

        {{-- Delete Account --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-rose-600 dark:text-rose-500 mb-1">Xóa tài khoản</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-xl">
                Một khi tài khoản bị xóa, mọi dữ liệu và lịch sử đặt phòng sẽ bị xóa vĩnh viễn. Vui lòng cân nhắc kỹ trước khi thực hiện.
            </p>

            <button type="button" onclick="document.getElementById('confirmDeleteModal').classList.remove('hidden')" 
                    class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-bold py-2.5 px-6 rounded-lg border border-rose-200 dark:border-rose-800/50 hover:bg-rose-200 dark:hover:bg-rose-900/50 transition-colors">
                Xóa tài khoản
            </button>

            {{-- Confirmation Modal (Vanilla JS/CSS) --}}
            <div id="confirmDeleteModal" class="{{ $errors->userDeletion->isNotEmpty() ? '' : 'hidden' }} fixed inset-0 z-[100] flex items-center justify-center">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('confirmDeleteModal').classList.add('hidden')"></div>
                <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-700 m-4">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Bạn có chắc chắn muốn xóa?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Hành động này không thể hoàn tác. Vui lòng nhập mật khẩu để xác nhận.</p>

                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Mật khẩu</label>
                            <input type="password" name="password" required autofocus
                                   class="w-full border border-slate-200 dark:border-slate-600 rounded-lg p-3 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition">
                            @error('password', 'userDeletion')
                                <p class="mt-2 text-sm text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('confirmDeleteModal').classList.add('hidden')"
                                    class="px-5 py-2.5 rounded-lg text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Hủy bỏ
                            </button>
                            <button type="submit" class="bg-rose-600 hover:bg-rose-500 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition-colors shadow">
                                Xác nhận xóa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection