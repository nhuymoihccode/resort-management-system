<x-guest-layout>
    <x-slot name="heading">Xác thực Email</x-slot>
    <x-slot name="subtitle">Kiểm tra hộp thư</x-slot>
 
    {{-- Email icon --}}
    <div style="display:flex;justify-content:center;margin-bottom:1.25rem">
        <div style="width:3.5rem;height:3.5rem;background:rgba(245,158,11,.1);border-radius:.875rem;display:flex;align-items:center;justify-content:center">
            <svg style="width:1.75rem;height:1.75rem;color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>
    </div>
 
    <p style="font-size:.875rem;color:#64748b;line-height:1.6;text-transform:none;letter-spacing:0;margin-bottom:1rem;text-align:center">
        Cảm ơn bạn đã đăng ký! Vui lòng kiểm tra email và nhấp vào link xác thực chúng tôi vừa gửi.
    </p>
 
    @if (session('status') == 'verification-link-sent')
        <div style="margin-bottom:1rem;padding:.75rem 1rem;border-radius:.625rem;background:#f0fdf4;border:1px solid #bbf7d0;font-size:.875rem;color:#15803d;text-align:center;text-transform:none;letter-spacing:0">
            Email xác thực mới đã được gửi!
        </div>
    @endif
 
    <div style="display:flex;flex-direction:column;gap:.75rem;margin-top:1.25rem">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button style="width:100%;justify-content:center">
                Gửi lại email xác thực
            </x-primary-button>
        </form>
 
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    style="width:100%;padding:.6rem 1rem;font-size:.875rem;font-weight:600;color:#94a3b8;background:none;border:none;cursor:pointer;text-transform:none;letter-spacing:0;transition:color .2s"
                    onmouseover="this.style.color='#64748b'" onmouseout="this.style.color='#94a3b8'">
                Đăng xuất
            </button>
        </form>
    </div>
</x-guest-layout>