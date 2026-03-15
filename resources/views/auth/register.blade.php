<x-guest-layout>
    <x-slot name="heading">Đăng ký</x-slot>
    <x-slot name="subtitle">Bắt đầu hành trình</x-slot>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Họ và tên" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nguyễn Văn A" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="ten@email.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" value="Mật khẩu" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button type="submit" class="w-full mt-6">
            Tạo tài khoản →
        </button>
    </form>

    <p class="mt-8 text-center text-sm font-medium text-slate-500 dark:text-slate-400">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Đăng nhập</a>
    </p>
</x-guest-layout>