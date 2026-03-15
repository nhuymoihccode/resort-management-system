<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Resort Pro' }}</title>

    {{-- Anti-flash dark mode script --}}
    <script>
    (function(){
        var s=localStorage.getItem('resort-theme'),d=window.matchMedia('(prefers-color-scheme:dark)').matches;
        var dark=s==='dark'||(!s&&d);
        document.documentElement.classList.toggle('dark',dark);
        document.documentElement.classList.toggle('light',!dark);
    })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{serif:['"Playfair Display"','serif'],sans:['Inter','sans-serif']}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        /* ============================================================
           SINGLE SOURCE OF TRUTH CSS 
           Manages the UI for all Auth pages
        ============================================================ */
        * { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; box-sizing: border-box; }

        /* Labels */
        #authWrapper label.block.font-medium,
        #authWrapper .auth-form label {
            font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #64748b; margin-bottom: .5rem; display: block;
        }
        html.dark #authWrapper label.block.font-medium,
        html.dark #authWrapper .auth-form label { color: #94a3b8; }

        /* Inputs */
        #authWrapper .auth-form input[type="text"],
        #authWrapper .auth-form input[type="email"],
        #authWrapper .auth-form input[type="password"] {
            width: 100%; padding: .875rem 1rem; border-radius: .75rem; border: 1.5px solid #e2e8f0;
            background: #f8fafc; color: #1e293b; font-size: 1rem; font-weight: 500;
            outline: none; box-shadow: none; transition: border-color .2s, box-shadow .2s;
        }
        html.dark #authWrapper .auth-form input[type="text"],
        html.dark #authWrapper .auth-form input[type="email"],
        html.dark #authWrapper .auth-form input[type="password"] {
            background: #0f172a; border-color: #334155; color: #f1f5f9;
        }
        #authWrapper .auth-form input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.12); }
        #authWrapper .auth-form input::placeholder { color: #94a3b8; font-weight: 400; }

        /* Primary Submit Button */
        #authWrapper .auth-form button[type="submit"] {
            width: 100%; display: flex; justify-content: center; align-items: center; /* Force full width */
            background: #d97706; color: #fff; font-weight: 700; font-size: .9375rem; letter-spacing: .06em;
            padding: .875rem 1.75rem; border-radius: .875rem; border: none;
            box-shadow: 0 4px 12px rgba(217,119,6,.2); cursor: pointer; transition: background .2s, transform .1s, box-shadow .2s;
            text-transform: uppercase; margin-top: 1.5rem;
        }
        #authWrapper .auth-form button[type="submit"]:hover { background: #b45309; box-shadow: 0 4px 16px rgba(180,83,9,.3); }
        #authWrapper .auth-form button[type="submit"]:active { transform: scale(.98); }

        /* Secondary Button (e.g., Logout in Verify Email page) */
        #authWrapper .auth-form button.secondary-btn {
            background: transparent; color: #94a3b8; box-shadow: none; text-transform: none; letter-spacing: normal;
            border: 1px solid #e2e8f0; margin-top: 0.5rem;
        }
        html.dark #authWrapper .auth-form button.secondary-btn { border-color: #334155; }
        #authWrapper .auth-form button.secondary-btn:hover { color: #64748b; background: #f1f5f9; }
        html.dark #authWrapper .auth-form button.secondary-btn:hover { color: #cbd5e1; background: #1e293b; }

        /* Validation Errors */
        #authWrapper .auth-form p[class*="text-red"],
        #authWrapper .auth-form ul[class*="text-red"] {
            font-size: .8125rem; color: #dc2626; margin-top: .4rem; list-style: none; padding-left: 0; font-weight: 500;
        }
        html.dark #authWrapper .auth-form p[class*="text-red"],
        html.dark #authWrapper .auth-form ul[class*="text-red"] { color: #f87171; }

        /* Remember Me Checkbox Group */
        #authWrapper .auth-form .checkbox-group {
            display: flex; align-items: center; gap: 0.625rem; cursor: pointer; margin-top: 1rem;
        }
        #authWrapper .auth-form .checkbox-group input[type="checkbox"] {
            accent-color: #d97706; width: 1.1rem; height: 1.1rem; border-radius: .25rem; cursor: pointer; margin: 0;
        }
        #authWrapper .auth-form .checkbox-group span {
            font-size: .875rem; font-weight: 500; text-transform: none; letter-spacing: normal; color: #475569;
        }
        html.dark #authWrapper .auth-form .checkbox-group span { color: #94a3b8; }

        /* Links */
        #authWrapper .auth-form a { color: #d97706; font-weight: 600; text-decoration: none; transition: color .2s; }
        html.dark #authWrapper .auth-form a { color: #fbbf24; }
        #authWrapper .auth-form a:hover { color: #b45309; text-decoration: underline; }

        /* Session Status Alert */
        #authWrapper .auth-form [class*="text-green"] {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: .625rem; padding: .875rem 1rem;
            color: #15803d; font-size: .875rem; margin-bottom: 1.5rem; font-weight: 500; text-align: center;
        }
        html.dark #authWrapper .auth-form [class*="text-green"] {
            background: rgba(22,163,74,.1); border-color: rgba(22,163,74,.3); color: #4ade80;
        }

        /* Dark Mode Toggle Button */
        #darkToggle svg { pointer-events: none; }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-200 min-h-screen flex antialiased" id="authWrapper">

    {{-- Left Panel: Resort Image (Desktop Only) --}}
    <div class="hidden lg:flex lg:w-[52%] relative overflow-hidden flex-shrink-0 shadow-2xl">
        <img src="https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&cs=tinysrgb&w=1200"
             class="absolute inset-0 w-full h-full object-cover" alt="Resort Pro">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/85 via-slate-900/50 to-transparent"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            <a href="{{ route('home') }}" class="font-serif font-bold tracking-widest text-xl text-white hover:text-amber-400 transition-colors self-start">RESORT PRO</a>
            <div>
                <p class="text-amber-400 text-xs font-bold tracking-[.25em] uppercase mb-3">Kỳ nghỉ đẳng cấp</p>
                <h2 class="text-4xl font-serif text-white leading-snug mb-4">Nơi thời gian<br><em class="not-italic text-amber-400">ngừng trôi.</em></h2>
                <p class="text-slate-300 text-base leading-relaxed max-w-sm">Trải nghiệm không gian nghỉ dưỡng sang trọng, nơi thiên nhiên và đẳng cấp hòa quyện.</p>
                <div class="flex items-center gap-2 mt-6">
                    <div class="flex text-amber-400">
                        @for($i=0;$i<5;$i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-slate-300 text-sm font-medium">4.9 · 128 đánh giá</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel: Auth Form --}}
    <div class="flex-1 flex flex-col min-h-screen">
        
        {{-- Mobile Header & Theme Toggle --}}
        <div class="flex items-center justify-between px-6 pt-6 lg:justify-end relative z-20">
            <a href="{{ route('home') }}" class="lg:hidden font-serif font-bold tracking-widest text-lg text-slate-900 dark:text-white">RESORT PRO</a>
            <button id="darkToggle" aria-label="Chuyển giao diện" class="p-2.5 rounded-full border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-amber-400 dark:hover:border-amber-400 transition-all duration-200 bg-white dark:bg-slate-800 shadow-sm">
                <svg class="w-4 h-4 text-amber-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
        </div>

        {{-- Form Area --}}
        <div class="flex-1 flex items-center justify-center lg:justify-start px-6 lg:px-16 xl:px-24 py-10 relative z-10">
            <div class="w-full max-w-md lg:max-w-lg xl:max-w-xl">
                
                {{-- Dynamic Heading --}}
                <div class="mb-10">
                    <p class="text-[12px] font-bold tracking-[.25em] uppercase text-amber-600 dark:text-amber-400 mb-3">
                        {{ $subtitle ?? 'Tài khoản' }}
                    </p>
                    <h1 class="text-4xl lg:text-5xl font-serif font-bold text-slate-900 dark:text-white">
                        {{ $heading ?? 'Bảo mật' }}
                    </h1>
                </div>

                {{-- Injected Component Content --}}
                <div class="auth-form space-y-5">
                    {{ $slot }}
                </div>

                {{-- Back to Home Link --}}
                <div class="mt-10 pt-8 border-t border-slate-200 dark:border-slate-700/60">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Về trang chủ Resort Pro
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('darkToggle').addEventListener('click', function(){
            var dark = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark',  dark);
            document.documentElement.classList.toggle('light', !dark);
            localStorage.setItem('resort-theme', dark ? 'dark' : 'light');
        });
    </script>
</body>
</html>