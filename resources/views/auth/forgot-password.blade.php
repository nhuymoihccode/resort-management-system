{{-- ══ forgot-password.blade.php ══ --}}
<x-guest-layout>
    <x-slot name="heading">Quên mật khẩu?</x-slot>
    <x-slot name="subtitle">Khôi phục tài khoản</x-slot>
 
    <p style="font-size:.875rem;color:#64748b;line-height:1.6;text-transform:none;letter-spacing:0;margin-bottom:1.25rem">
        Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.
    </p>
 
    <x-auth-session-status class="mb-4" :status="session('status')" />
 
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autofocus
                          placeholder="ten@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-5">
            <x-primary-button style="width:100%;justify-content:center">
                Gửi link đặt lại →
            </x-primary-button>
        </div>
    </form>
 
    <p style="margin-top:1.25rem;text-align:center;font-size:.875rem;text-transform:none;letter-spacing:0">
        <a href="{{ route('login') }}" style="color:#d97706;font-weight:600;text-decoration:none">
            ← Quay lại đăng nhập
        </a>
    </p>
</x-guest-layout>