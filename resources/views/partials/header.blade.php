{{-- ═══════════════════════════════════════════════════════════
     HEADER / NAVBAR — Shared Partial
     Include via: @include('partials.header')
     ═══════════════════════════════════════════════════════════ --}}
@php
    use Illuminate\Support\Str;
    $siteName = \App\Models\Setting::get('site_name', 'Atomni');
    $logoLight = \App\Models\Setting::get('site_logo');
    $logoDark = \App\Models\Setting::get('site_logo_dark');
@endphp

<header id="site-header" class="fixed inset-x-0 bg-navy-950/80 backdrop-blur-xl border-b border-navy-700/50 light:bg-white/80 light:border-slate-200" style="top:30px;z-index:50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-4 focus:ring-offset-navy-950">
                @if($logoLight && $logoDark)
                <img src="{{ asset('storage/' . $logoLight) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain logo-light">
                <img src="{{ asset('storage/' . $logoDark) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain logo-dark">
            @elseif($logoLight)
                <img src="{{ asset('storage/' . $logoLight) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain">
            @elseif($logoDark)
                <img src="{{ asset('storage/' . $logoDark) }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain">
            @else
                <img src="{{ asset('images/atomni-logo-light.svg') }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain logo-light">
                <img src="{{ asset('images/atomni-logo-dark.svg') }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain logo-dark">
            @endif
            </a>

            {{-- Nav Links (desktop) — Dynamic from DB --}}
            <nav class="hidden xl:flex flex-1 items-center gap-1 mx-4 whitespace-nowrap">
                <a href="{{ url('/') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors shrink-0 {{ request()->is('/') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100' }}">
                    Home
                </a>
                @if($headerMenu && $headerMenu->rootItems)
                    @foreach($headerMenu->rootItems as $navItem)
                        @php
                            $slug = null;
                            $urlPath = trim($navItem->url, '/');
                            if (Str::startsWith($urlPath, 'category/')) {
                                $slug = Str::after($urlPath, 'category/');
                            }
                            $categoryData = $slug ? ($categoriesWithSubs[$slug] ?? null) : null;
                        @endphp
                        
                        @if($categoryData)
                            {{-- Dropdown / Megamenu Group --}}
                            <div class="relative group shrink-0">
                                <a href="{{ url($navItem->url) }}"
                                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1 {{ request()->is(ltrim($navItem->url, '/') . '*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100' }}">
                                    {{ $navItem->title }}
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </a>
                                
                                {{-- Megamenu Panel --}}
                                <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-[720px] bg-navy-950/95 border border-navy-700/80 rounded-2xl shadow-2xl p-6 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200 z-50 backdrop-blur-xl grid grid-cols-12 gap-6 light:bg-white/95 light:border-slate-200">
                                    {{-- Left Column: Subcategories list --}}
                                    <div class="col-span-4 border-r border-navy-800/50 light:border-slate-100 pr-6 text-left">
                                        <h4 class="text-xs font-bold text-text-muted uppercase tracking-wider mb-4">Sub Categories</h4>
                                        <div class="space-y-1">
                                            @foreach($categoryData->children as $subCat)
                                                <a href="{{ route('category', $subCat->slug) }}" class="px-3 py-2 rounded-lg text-sm font-medium text-text-secondary hover:text-electric hover:bg-electric/5 transition-all flex items-center justify-between">
                                                    <span>{{ $subCat->name }}</span>
                                                    <span class="text-xs text-text-muted bg-navy-800/40 px-2 py-0.5 rounded-full light:bg-slate-100">{{ $subCat->posts_count }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    {{-- Right Column: Featured/Latest posts --}}
                                    <div class="col-span-8 text-left">
                                        <h4 class="text-xs font-bold text-text-muted uppercase tracking-wider mb-4">Latest in {{ $categoryData->name }}</h4>
                                        @if($categoryData->latest_posts->isNotEmpty())
                                            <div class="grid grid-cols-3 gap-4">
                                                @foreach($categoryData->latest_posts as $p)
                                                    <a href="{{ url($p->slug) }}" class="group/post block space-y-2">
                                                        <div class="aspect-video w-full rounded-lg overflow-hidden bg-navy-800 light:bg-slate-100 relative">
                                                            @if($p->featured_image)
                                                                <img src="{{ asset('storage/' . $p->featured_image) }}" alt="{{ $p->title }}" class="w-full h-full object-cover transition-transform group-hover/post:scale-105 duration-300">
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center text-text-muted">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <h5 class="text-xs font-bold text-text-primary line-clamp-2 hover:text-electric transition-colors whitespace-normal">
                                                            {{ $p->title }}
                                                        </h5>
                                                        <span class="text-[10px] text-text-muted block">
                                                            {{ $p->published_at ? $p->published_at->format('M d, Y') : '' }}
                                                        </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="h-32 flex items-center justify-center text-sm text-text-muted">
                                                No recent posts.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Standard Nav Item --}}
                            <a href="{{ url($navItem->url) }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition-colors shrink-0 {{ request()->is(ltrim($navItem->url, '/') . '*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100' }}">
                                {{ $navItem->title }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </nav>

            {{-- Right actions --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Search --}}
                <div class="relative group hidden sm:block js-search-container">
                    <form action="{{ route('search') }}" method="GET" class="relative flex items-center">
                        <input type="text" name="q" placeholder="Search..." autocomplete="off" class="js-autocomplete-search w-8 focus:w-48 lg:focus:w-64 pl-9 pr-3 py-2 rounded-full bg-navy-800/40 border border-transparent focus:border-electric focus:bg-navy-900/60 light:bg-slate-100 light:focus:bg-white light:border-slate-200 text-sm text-text-primary placeholder:text-transparent focus:placeholder:text-text-muted transition-all duration-300 ease-out outline-none cursor-pointer focus:cursor-text">
                        <button type="submit" class="absolute left-0 top-0 bottom-0 px-2.5 text-text-secondary hover:text-text-primary transition-colors pointer-events-auto" aria-label="Search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                    {{-- Autocomplete Dropdown --}}
                    <div class="js-search-dropdown absolute top-full right-0 mt-2 w-72 bg-navy-900 light:bg-white border border-navy-700 light:border-slate-200 rounded-xl shadow-xl overflow-hidden z-50 hidden opacity-0 transition-opacity duration-200">
                        <ul class="js-search-results divide-y divide-navy-800 light:divide-slate-100"></ul>
                        <div class="js-search-loading hidden p-4 text-center text-sm text-text-muted">
                            <svg class="animate-spin h-5 w-5 mx-auto text-electric" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                {{-- Mobile Search Icon --}}
                <a href="{{ url('search') }}" class="p-2 sm:hidden rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100 transition-colors" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>
                {{-- Dark/Light mode toggle --}}
                <button id="theme-toggle" class="relative p-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100 transition-colors" aria-label="Toggle theme">
                    <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                {{-- Subscribe CTA (Desktop + Mobile) --}}
                <a href="#newsletter" class="sm:hidden p-2 rounded-lg text-electric hover:bg-navy-800/60 light:hover:bg-slate-100 transition-colors" aria-label="Subscribe">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </a>
                <a href="#newsletter" class="hidden sm:inline-flex items-center px-4 py-2 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20 hover:shadow-electric/40">
                    Subscribe
                </a>
                {{-- Explore Topics Button --}}
                <a href="{{ route('explore') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100 transition-colors text-sm font-medium" aria-label="Explore Topics">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Explore
                </a>
                {{-- Mobile Hamburger --}}
                <button id="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Open mobile menu" class="xl:hidden p-3 -m-1 rounded-lg text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-electric">
                    <svg id="mega-icon-bars" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>
</header>


{{-- Mobile nav fallback (shown on small screens) --}}
<div id="mobile-nav-panel" class="xl:hidden fixed inset-0 bg-navy-950/98 light:bg-white/98 backdrop-blur-xl transform translate-x-full transition-transform duration-300 ease-out pointer-events-none" style="z-index:60">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between px-4 h-16 border-b border-navy-700/50 light:border-slate-200">
            <span class="text-lg font-bold text-text-primary">Menu</span>
            <button onclick="closeMobileNav()" class="p-2 text-text-secondary hover:text-text-primary rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is('/') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60' }}">Home</a>
            @if($headerMenu && $headerMenu->rootItems)
                @foreach($headerMenu->rootItems as $navItem)
                    @php
                        $slug = null;
                        $urlPath = trim($navItem->url, '/');
                        if (Str::startsWith($urlPath, 'category/')) {
                            $slug = Str::after($urlPath, 'category/');
                        }
                        $categoryData = $slug ? ($categoriesWithSubs[$slug] ?? null) : null;
                    @endphp
                    
                    @if($categoryData)
                        <div class="space-y-1">
                            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-navy-800/60 light:hover:bg-slate-100">
                                <a href="{{ url($navItem->url) }}" class="text-sm font-medium text-text-secondary hover:text-text-primary">
                                    {{ $navItem->title }}
                                </a>
                                <button onclick="toggleMobileSubmenu('sub-{{ $categoryData->slug }}')" class="p-2 text-text-muted hover:text-text-primary transition-transform" id="btn-sub-{{ $categoryData->slug }}" aria-label="Toggle submenu">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                            <div id="sub-{{ $categoryData->slug }}" class="hidden pl-4 space-y-1">
                                @foreach($categoryData->children as $subCat)
                                    <a href="{{ route('category', $subCat->slug) }}" class="block px-3 py-2 rounded-lg text-xs font-medium text-text-muted hover:text-electric hover:bg-electric/5 transition-all">
                                        — {{ $subCat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ url($navItem->url) }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-navy-800/60 light:hover:bg-slate-100">{{ $navItem->title }}</a>
                    @endif
                @endforeach
            @endif
            <div class="pt-4 mt-4 border-t border-navy-700/30 light:border-slate-200">
                <a href="{{ route('explore') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-navy-800/60 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Explore All Topics
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const btn = document.getElementById('mobile-menu-toggle');
    const mobilePanel = document.getElementById('mobile-nav-panel');

    if(btn) {
        btn.addEventListener('click', function() {
            mobilePanel.classList.remove('translate-x-full', 'pointer-events-none');
            mobilePanel.classList.add('translate-x-0', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
            btn.setAttribute('aria-expanded', 'true');
        });
    }

    window.closeMobileNav = function() {
        if(mobilePanel) {
            mobilePanel.classList.add('translate-x-full', 'pointer-events-none');
            mobilePanel.classList.remove('translate-x-0', 'pointer-events-auto');
        }
        document.body.style.overflow = '';
        if(btn) btn.setAttribute('aria-expanded', 'false');
    };

    // Auto-close mobile nav when a link is clicked
    if(mobilePanel) {
        mobilePanel.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                window.closeMobileNav();
            });
        });
    }

    document.addEventListener('keydown', function(e) {
        if(e.key === 'Escape') {
            window.closeMobileNav();
        }
    });

    window.toggleMobileSubmenu = function(id) {
        const panel = document.getElementById(id);
        const btn = document.getElementById('btn-' + id);
        if (panel) {
            panel.classList.toggle('hidden');
            if (btn) {
                btn.querySelector('svg').classList.toggle('rotate-180');
            }
        }
    };
})();
</script>
