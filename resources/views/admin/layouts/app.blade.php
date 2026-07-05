<!DOCTYPE html>
@php
    $theme_type = \App\Models\Setting::get('theme_type', 'preset');
    $font_family = \App\Models\Setting::get('font_family', 'Inter');
    if ($font_family === 'inter') $font_family = 'Inter';
    if ($font_family === 'roboto') $font_family = 'Roboto';
    if ($font_family === 'dm_sans') $font_family = 'DM Sans';
    
    $encoded_font = str_replace(' ', '+', $font_family);
    $font_css = "'{$font_family}', sans-serif";
    
    $primary = \App\Models\Setting::get('theme_manual_primary', '#2D7FF9');
    $secondary = \App\Models\Setting::get('theme_manual_secondary', '#1A5FD1');

    $themeVars = "--font-sans: {$font_css} !important; --font-heading: {$font_css} !important;";
    if ($theme_type === 'manual') {
        $themeVars .= " --color-electric: {$primary} !important; --color-accent-blue: {$primary} !important; --color-accent-blue-hover: {$secondary} !important; --color-cyan-glow: {$secondary} !important; --color-brand-primary: {$primary} !important; --color-brand-secondary: {$secondary} !important; --color-electric-dark: {$secondary} !important; --color-electric-light: color-mix(in srgb, {$primary} 70%, white) !important;";
    }
@endphp
<html lang="en" class="scroll-smooth theme-{{ \App\Models\Setting::get('theme_color', 'blue') }}" style="{{ $themeVars }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Atomni</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    @php $favicon = \App\Models\Setting::get('site_favicon', ''); @endphp
    @if($favicon && file_exists(public_path('storage/' . $favicon)))
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}?v={{ @filemtime(public_path('storage/' . $favicon)) ?: time() }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $favicon) }}?v={{ @filemtime(public_path('storage/' . $favicon)) ?: time() }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $favicon) }}?v={{ @filemtime(public_path('storage/' . $favicon)) ?: time() }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚙️</text></svg>">
        <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚙️</text></svg>">
        <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚙️</text></svg>">
    @endif

    <script>
        (function() {
            var t = localStorage.getItem('atomni-theme');
            if (t === 'light') document.documentElement.classList.add('light');
        })();
    </script>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link id="app-font" href="https://fonts.googleapis.com/css2?family={{ $encoded_font }}:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-navy-950 text-text-primary font-sans antialiased min-h-screen transition-colors duration-300 overflow-x-hidden max-w-full">

    {{-- Mobile sidebar backdrop --}}
    <div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm md:hidden" onclick="closeSidebar()"></div>

    <div class="flex min-h-screen w-full max-w-full overflow-x-hidden relative">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col md:ml-64 min-w-0 w-full">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 w-full max-w-full bg-navy-900/80 backdrop-blur-xl border-b border-navy-700/50 px-4 sm:px-6 py-3 sm:py-4 light:bg-white/80 light:border-slate-200">
                <div class="flex items-center justify-between min-w-0 gap-2">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <button id="admin-sidebar-toggle" aria-label="Toggle sidebar" class="md:hidden p-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-950 transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="font-heading font-bold text-lg text-text-primary page-title truncate">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        {{-- Theme toggle --}}
                        <button id="theme-toggle" class="p-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-950 transition-colors" aria-label="Toggle theme">
                            <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                        {{-- Visit site --}}
                        <a href="/" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-text-secondary hover:text-text-primary hover:bg-navy-800/60 transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            View Site
                        </a>
                        {{-- User Dropdown --}}
                        <div class="relative pl-3 border-l border-navy-700/50">
                            <button id="user-menu-button" class="flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-950 rounded-lg p-1 transition-colors hover:bg-navy-800/60" aria-expanded="false" aria-haspopup="true">
                                @if(auth()->user()->profile_image)
                                    <div class="w-8 h-8 rounded-full overflow-hidden border border-navy-700/50">
                                        <img loading="lazy" src="{{ \Illuminate\Support\Str::startsWith(auth()->user()->profile_image, 'http') ? auth()->user()->profile_image : \Illuminate\Support\Facades\Storage::url(auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover" onerror="this.parentElement.outerHTML=`<div class='w-8 h-8 rounded-full bg-electric/20 flex items-center justify-center shrink-0'><span class='text-electric text-sm font-semibold'>{{ substr(auth()->user()?->name ?? 'A', 0, 1) }}</span></div>`">
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-electric/20 flex items-center justify-center shrink-0">
                                        <span class="text-electric text-sm font-semibold">{{ substr(auth()->user()?->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="hidden sm:block text-sm text-text-secondary">{{ auth()->user()?->name ?? 'Admin' }}</span>
                                <svg class="w-4 h-4 text-text-muted hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-48 rounded-xl bg-navy-900 border border-navy-700/50 shadow-xl shadow-black/20 origin-top-right transition-all duration-200 opacity-0 invisible scale-95 pointer-events-none z-50">
                                <div class="p-1">
                                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-text-secondary hover:text-text-primary hover:bg-navy-800 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        My Profile
                                    </a>
                                </div>
                                <div class="px-3 py-2 border-t border-navy-700/50 p-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-4 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-success/10 border border-success/30 text-success text-sm flex items-center gap-2 flash-message">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="flex-1 min-w-0">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-alert-red/10 border border-alert-red/30 text-alert-red text-sm flex items-center gap-2 flash-message">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="flex-1 min-w-0">{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="mx-4 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-amber/10 border border-amber/30 text-amber text-sm flex items-center gap-2 flash-message">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="flex-1 min-w-0">{{ session('warning') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mx-4 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-alert-red/10 border border-alert-red/30 text-alert-red text-sm flash-message">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-semibold">Please fix the errors below:</span>
                    </div>
                    <ul class="ml-6 space-y-0.5 list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-5 lg:p-6 min-w-0 w-full max-w-full overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

    <x-admin.media-selector />

    <script>
        // Theme toggle
        (function() {
            var html = document.documentElement;
            var btn  = document.getElementById('theme-toggle');
            var moon = document.getElementById('theme-icon-moon');
            var sun  = document.getElementById('theme-icon-sun');
            function applyIcons() {
                var isLight = html.classList.contains('light');
                if (moon) moon.classList.toggle('hidden', isLight);
                if (sun)  sun.classList.toggle('hidden', !isLight);
            }
            applyIcons();
            if (btn) {
                btn.addEventListener('click', function() {
                    html.classList.toggle('light');
                    localStorage.setItem('atomni-theme', html.classList.contains('light') ? 'light' : 'dark');
                    applyIcons();
                });
            }
        })();

        // Mobile sidebar open / close
        var sidebar  = document.getElementById('admin-sidebar');
        var backdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            if (sidebar) sidebar.style.left = '0';
            backdrop?.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            if (sidebar) sidebar.style.left = '-16rem';
            backdrop?.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('admin-sidebar-toggle')?.addEventListener('click', function() {
            var isOpen = sidebar && sidebar.style.left === '0px' || sidebar.style.left === '0';
            isOpen ? closeSidebar() : openSidebar();
        });

        // Auto-close sidebar on mobile when any nav link is clicked
        if (sidebar) {
            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) closeSidebar();
                });
            });
        }

        // Auto-dismiss overlay on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                backdrop?.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // User dropdown logic
        const userBtn = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-menu-dropdown');
        if(userBtn && userDropdown) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isExpanded = userBtn.getAttribute('aria-expanded') === 'true';
                userBtn.setAttribute('aria-expanded', !isExpanded);
                
                if(!isExpanded) {
                    userDropdown.classList.remove('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
                    userDropdown.classList.add('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
                } else {
                    userDropdown.classList.add('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
                    userDropdown.classList.remove('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if(!userBtn.contains(e.target) && !userDropdown.contains(e.target) && userBtn.getAttribute('aria-expanded') === 'true') {
                    userBtn.setAttribute('aria-expanded', 'false');
                    userDropdown.classList.add('opacity-0', 'invisible', 'scale-95', 'pointer-events-none');
                    userDropdown.classList.remove('opacity-100', 'visible', 'scale-100', 'pointer-events-auto');
                }
            });
        }
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>

