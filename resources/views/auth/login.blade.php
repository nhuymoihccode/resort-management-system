<x-guest-layout>
    <x-slot name="heading">Đăng nhập</x-slot>
    <x-slot name="subtitle">Chào mừng trở lại</x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        
        @if(request()->has('redirect_to'))
            <input type="hidden" name="redirect_to" value="{{ request()->query('redirect_to') }}">
        @endif

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ten@email.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" value="Mật khẩu" class="!mb-0" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-amber-600 hover:underline">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="pt-1 pb-2">
            <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400" style="text-transform:none;letter-spacing:0">Ghi nhớ đăng nhập</span>
            </label>
        </div>

        <button type="submit" class="w-full mt-2">
            Đăng nhập →
        </button>
    </form>

    <p class="mt-8 text-center text-sm font-medium text-slate-500 dark:text-slate-400">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Đăng ký ngay</a>
    </p>
</x-guest-layout>