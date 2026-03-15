<x-guest-layout>
    <x-slot name="heading">Xác nhận mật khẩu</x-slot>
    <x-slot name="subtitle">Khu vực bảo mật</x-slot>

    <p
        style="font-size:.875rem;color:#64748b;line-height:1.6;text-transform:none;letter-spacing:0;margin-bottom:1.25rem">
        Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu để tiếp tục.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div>
            <x-input-label for="password" value="Mật khẩu" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-5">
            <x-primary-button style="width:100%;justify-content:center">
                Xác nhận →
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>