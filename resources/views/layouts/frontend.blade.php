<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Resort Pro')</title>

    {{-- Theme Anti-flash: Prevents FOUC (Flash of Unstyled Content) during initial load --}}
    <script>
    (function(){
        var s=localStorage.getItem('resort-theme'),d=window.matchMedia('(prefers-color-scheme:dark)').matches;
        var dark=s==='dark'||(!s&&d);
        document.documentElement.classList.toggle('dark',dark);
        document.documentElement.classList.toggle('light',!dark);
    })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{serif:['"Playfair Display"','serif'],sans:['Inter','sans-serif']}}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        #mainNav { position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: padding .3s ease, background .3s ease, box-shadow .3s ease; }
        #mainNav[data-state="transparent"] { padding: 1.1rem 0; background: rgba(0,0,0,0.18); backdrop-filter: blur(4px); border-bottom: 1px solid rgba(255,255,255,0.08); }
        #mainNav[data-state="transparent"] .nav-logo { color: #fff; }
        #mainNav[data-state="transparent"] .nav-link { color: rgba(255,255,255,.88); }
        #mainNav[data-state="transparent"] .nav-link:hover { color: #fbbf24; }
        #mainNav[data-state="transparent"] .nav-icon { color: rgba(255,255,255,.65); }
        #mainNav[data-state="transparent"] .nav-border{ border-color: rgba(255,255,255,.25); }
        #mainNav[data-state="transparent"] .nav-login { color: rgba(255,255,255,.88); border-color: rgba(255,255,255,.3); }
        #mainNav[data-state="transparent"] .nav-login:hover { background: rgba(255,255,255,.1); border-color: #fbbf24; }

        #mainNav[data-state="dark"] { padding: .6rem 0; background: rgba(15,23,42,.97); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(255,255,255,.07); box-shadow: 0 4px 24px rgba(0,0,0,.28); }
        #mainNav[data-state="dark"] .nav-logo { color: #fff; }
        #mainNav[data-state="dark"] .nav-link { color: rgba(255,255,255,.85); }
        #mainNav[data-state="dark"] .nav-link:hover { color: #fbbf24; }
        #mainNav[data-state="dark"] .nav-icon { color: rgba(255,255,255,.6); }
        #mainNav[data-state="dark"] .nav-border{ border-color: rgba(255,255,255,.2); }
        #mainNav[data-state="dark"] .nav-login { color: rgba(255,255,255,.85); border-color: rgba(255,255,255,.25); }
        #mainNav[data-state="dark"] .nav-login:hover { background: rgba(255,255,255,.08); border-color: #fbbf24; }

        #mainNav[data-state="light"] { padding: .6rem 0; background: rgba(255,255,255,.98); backdrop-filter: blur(16px); border-bottom: 1px solid #e2e8f0; box-shadow: 0 2px 16px rgba(0,0,0,.07); }
        #mainNav[data-state="light"] .nav-logo { color: #1e293b; }
        #mainNav[data-state="light"] .nav-link { color: #334155; }
        #mainNav[data-state="light"] .nav-link:hover { color: #d97706; }
        #mainNav[data-state="light"] .nav-icon { color: #64748b; }
        #mainNav[data-state="light"] .nav-border{ border-color: #cbd5e1; }
        #mainNav[data-state="light"] .nav-login { color: #334155; border-color: #cbd5e1; }
        #mainNav[data-state="light"] .nav-login:hover { background: #f8fafc; border-color: #d97706; color: #d97706; }

        #navLogo { font-size: 1.35rem; transition: font-size .3s ease; }
        #mainNav[data-state="dark"] #navLogo, #mainNav[data-state="light"] #navLogo { font-size: 1.1rem; }

        .toggle-thumb { transition: transform .28s cubic-bezier(.4,0,.2,1); }
        html.dark .toggle-thumb { transform: translateX(20px); }
        html.light .toggle-thumb { transform: translateX(0); }

        #mobileMenu { max-height: 0; opacity: 0; overflow: hidden; transition: max-height .3s ease, opacity .25s ease; }
        #mobileMenu.open { max-height: 280px; opacity: 1; }

        @keyframes shimmer { 0% { background-position: -400px 0; } 100% { background-position: 400px 0; } }
        .skeleton { background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%); background-size: 800px 100%; animation: shimmer 1.4s infinite; border-radius: 0.5rem; }
        html.dark .skeleton { background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%); background-size: 800px 100%; }

        html { scroll-behavior: smooth; }
        @keyframes pageFade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        main { animation: pageFade .4s ease both; }
    </style>
</head>

<body class="bg-white dark:bg-slate-900 text-slate-800 dark:text-gray-200 font-sans antialiased selection:bg-amber-500 selection:text-white flex flex-col min-h-screen transition-colors duration-300">

    <nav id="mainNav" data-state="transparent" data-has-hero="@yield('has_hero', 'false')" class="px-5 sm:px-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" id="navLogo" class="nav-logo font-serif font-bold tracking-widest transition-colors duration-300">
                RESORT PRO
            </a>

            <div class="hidden sm:flex items-center gap-5 text-[11px] font-bold tracking-[0.15em] uppercase">
                <a href="{{ route('rooms.index') }}" class="nav-link transition-colors duration-200">Phòng</a>

                <button id="themeToggle" aria-label="Chuyển giao diện" class="nav-border flex items-center gap-2 px-2.5 py-1.5 rounded-full border hover:border-amber-400 transition-all duration-200">
                    <svg class="w-3.5 h-3.5 text-amber-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <svg class="nav-icon w-3.5 h-3.5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    <span class="relative w-9 h-5 rounded-full bg-slate-300 dark:bg-amber-500 transition-colors duration-300"><span class="toggle-thumb absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow-sm"></span></span>
                </button>

                @auth
                    {{-- User Profile Dropdown (Linked with Laravel Breeze) --}}
                    <div class="relative" id="userMenuWrap">
                        <button id="userMenuBtn" class="nav-border flex items-center gap-2 px-3 py-1.5 rounded-full border hover:border-amber-400 transition-all duration-200">
                            <span class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 1, 'UTF-8')) }}
                            </span>
                            <span class="nav-link text-xs font-semibold max-w-[90px] truncate">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="nav-icon w-3 h-3 transition-transform duration-200" id="userChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="userMenu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50 transition-all duration-200">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Tài khoản</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Hồ sơ cá nhân
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-700 m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login', ['redirect_to' => request()->fullUrl()]) }}" class="nav-login px-4 py-2 rounded-lg border transition-all duration-200">Đăng nhập</a>
                @endauth
            </div>

            <div class="sm:hidden flex items-center gap-2">
                <button id="themeToggleMobile" aria-label="Chuyển giao diện" class="nav-border flex items-center gap-1.5 px-2 py-1.5 rounded-full border hover:border-amber-400 transition-all">
                    <svg class="w-3.5 h-3.5 text-amber-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <svg class="nav-icon w-3.5 h-3.5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    <span class="relative w-8 h-4 rounded-full bg-slate-300 dark:bg-amber-500 transition-colors duration-300"><span class="toggle-thumb absolute top-0.5 left-0.5 w-3 h-3 rounded-full bg-white shadow"></span></span>
                </button>

                <button id="menuBtn" aria-label="Menu" class="nav-icon p-2 rounded-lg transition-colors">
                    <svg id="iconBurger" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="iconClose" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu">
            <div class="max-w-7xl mx-auto flex flex-col gap-1 pt-3 pb-4 mt-2 border-t border-white/10">
                <a href="{{ route('rooms.index') }}" class="nav-link text-sm font-semibold px-3 py-2.5 rounded-lg transition-colors uppercase tracking-wider">Phòng & Villa</a>
                @auth
                    <div class="mt-1 px-3 py-2 rounded-lg bg-white/10 dark:bg-slate-800/50">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="w-7 h-7 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 1, 'UTF-8')) }}
                            </span>
                            <div class="min-w-0">
                                <p class="nav-link text-sm font-semibold truncate normal-case tracking-normal">{{ Auth::user()->name }}</p>
                                <p class="nav-icon text-xs truncate normal-case tracking-normal opacity-70">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dashboard') }}" class="flex-1 text-center text-xs font-semibold py-1.5 px-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex-1 text-center text-xs font-semibold py-1.5 px-2 rounded-lg nav-login border transition-all">
                                Hồ sơ
                            </a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mt-1 w-full text-sm font-semibold px-3 py-2 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors text-center">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login', ['redirect_to' => request()->fullUrl()]) }}" class="nav-login mt-1 text-sm font-semibold px-3 py-2.5 rounded-lg border text-center transition-all">Đăng nhập</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-slate-100 dark:bg-black py-8 text-center text-slate-500 text-sm border-t border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <p>&copy; 2026 Resort Pro. Phát triển bởi Đặng Hoàng Nhật Huy.</p>
    </footer>

    <script>
    (function(){
        var nav = document.getElementById('mainNav');
        var hasHero = nav.dataset.hasHero === 'true';

        function calcState(){
            var dark = document.documentElement.classList.contains('dark');
            var scrolled = window.scrollY > 80;
            if(hasHero && !scrolled) return 'transparent';
            return dark ? 'dark' : 'light';
        }
        function updateNav(){ nav.dataset.state = calcState(); }
        window.addEventListener('scroll', updateNav, {passive:true});
        updateNav();

        function applyTheme(dark){
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.classList.toggle('light', !dark);
            localStorage.setItem('resort-theme', dark ? 'dark' : 'light');
            updateNav();
        }
        ['themeToggle','themeToggleMobile'].forEach(function(id){
            var b = document.getElementById(id);
            if(b) b.addEventListener('click', function(){ applyTheme(!document.documentElement.classList.contains('dark')); });
        });
        window.matchMedia('(prefers-color-scheme:dark)').addEventListener('change',function(e){
            if(!localStorage.getItem('resort-theme')) applyTheme(e.matches);
        });

        // Date Input Constraints & Preview Logic
        function todayISO(){
            var d=new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
        }
        function addDays(iso,n){
            var d=new Date(iso+'T00:00:00'); d.setDate(d.getDate()+n); return d.toISOString().split('T')[0];
        }
        function nightsBetween(a,b){
            return Math.round((new Date(b)-new Date(a))/(864e5));
        }

        function initDateGuard(){
            var ci = document.querySelector('input[name="check_in"]');
            var co = document.querySelector('input[name="check_out"]');
            var preview = document.getElementById('nightsPreviewWelcome');
            if(!ci||!co||ci.dataset.guarded) return;
            ci.dataset.guarded='1';
            var today = todayISO();

            ci.min = today;
            if(ci.value && ci.value < today) ci.value='';
            co.min = ci.value ? addDays(ci.value,1) : addDays(today,1);
            if(co.value && co.value <= (ci.value||today)) co.value='';

            function refreshPreview(){
                if(!preview) return;
                if(ci.value && co.value && co.value > ci.value){
                    var n = nightsBetween(ci.value, co.value);
                    preview.textContent = n + ' đêm';
                    preview.classList.remove('hidden');
                } else {
                    preview.classList.add('hidden');
                }
            }

            ci.addEventListener('change',function(){
                if(ci.value < today) ci.value = today;
                co.min = addDays(ci.value,1);
                if(co.value && co.value <= ci.value){
                    co.value='';
                    try{ co.showPicker(); }catch(e){}
                }
                refreshPreview();
            });
            co.addEventListener('change',function(){
                if(co.value && co.value <= ci.value) co.value = addDays(ci.value,1);
                refreshPreview();
            });
            refreshPreview();
        }
        initDateGuard();
        setTimeout(initDateGuard, 400);

        // Desktop User Menu Toggle
        var userMenuBtn = document.getElementById('userMenuBtn');
        var userMenu    = document.getElementById('userMenu');
        var userChevron = document.getElementById('userChevron');
        
        if(userMenuBtn && userMenu){
            userMenuBtn.addEventListener('click', function(e){
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
                if(userChevron) userChevron.style.transform = userMenu.classList.contains('hidden') ? '' : 'rotate(180deg)';
            });
            
            document.addEventListener('click', function(){
                userMenu.classList.add('hidden');
                if(userChevron) userChevron.style.transform = '';
            });
            
            userMenu.addEventListener('click', function(e){ e.stopPropagation(); });
        }

        // Mobile Navigation Toggle
        var menuBtn = document.getElementById('menuBtn');
        var mobileMenu = document.getElementById('mobileMenu');
        var iconBurger = document.getElementById('iconBurger');
        var iconClose = document.getElementById('iconClose');
        if(menuBtn){
            menuBtn.addEventListener('click',function(){
                var open = mobileMenu.classList.toggle('open');
                iconBurger.classList.toggle('hidden', open);
                iconClose.classList.toggle('hidden', !open);
            });
        }
    })();
    </script>
</body>
</html>