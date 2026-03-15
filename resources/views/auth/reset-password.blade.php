<x-guest-layout>
    <x-slot name="heading">Đặt lại mật khẩu</x-slot>
    <x-slot name="subtitle">Bảo mật tài khoản</x-slot>
 
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
 
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
 
        <div class="mt-4">
            <x-input-label for="password" value="Mật khẩu mới" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password"
                          required autocomplete="new-password"
                          placeholder="Tối thiểu 8 ký tự" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
 
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu mới" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password" name="password_confirmation"
                          required autocomplete="new-password"
                          placeholder="Nhập lại mật khẩu mới" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
 
        <div class="mt-5">
            <x-primary-button style="width:100%;justify-content:center">
                Đặt lại mật khẩu →
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>