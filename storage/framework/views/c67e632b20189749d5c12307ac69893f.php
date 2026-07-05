<!DOCTYPE html>
<?php
    $theme_color = \App\Models\Setting::get('theme_color', 'blue');
    $siteName    = \App\Models\Setting::get('site_name', 'Atomni');
    // Build a clean canonical URL — strip all tracking params (utm_*, fbclid, gclid, ref, etc.)
    // Pages set @section('canonical') explicitly; this fallback strips params from the live URL.
    $trackingParams = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content',
                       'fbclid','gclid','msclkid','ref','source','_ga'];
    $cleanUrl = request()->url(); // path only, no query string
    $canonicalUrl = View::hasSection('canonical')
        ? View::yieldContent('canonical')
        : $cleanUrl;
?>
<html lang="en" class="scroll-smooth theme-<?php echo e($theme_color); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $__env->yieldContent('meta-description', 'Atomni — AI workflow automation and business process solutions for service businesses, agencies, and operators.'); ?>">
    <meta name="robots" content="<?php echo $__env->yieldContent('robots', 'index,follow'); ?>">
    <meta name="google-site-verification" content="ADD_YOUR_VERIFICATION_CODE_HERE" />

    
    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">

    
    <meta property="og:type" content="<?php echo $__env->yieldContent('og-type', 'website'); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', '<?php echo e($siteName); ?> — News & Insights'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta-description', 'Atomni — Your premier source for breaking news, in-depth analysis, and trending stories.'); ?>">
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>">
    <meta property="og:site_name" content="<?php echo e($siteName); ?>">
    <meta property="og:locale" content="en_IN">
    <?php if(View::hasSection('og-image')): ?>
    <meta property="og:image" content="<?php echo $__env->yieldContent('og-image'); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo $__env->yieldContent('og-image-alt', $siteName . ' — Featured Image'); ?>">
    <?php endif; ?>

    
    <meta name="twitter:card" content="<?php echo $__env->yieldContent('twitter-card', 'summary_large_image'); ?>">
    <meta name="twitter:site" content="{{ trim(basename(\App\Models\Setting::get('social_twitter', '')), '/') ?: 'atomni' }}">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('title', '<?php echo e($siteName); ?> — News & Insights'); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('meta-description', 'Atomni — Your premier source for breaking news, in-depth analysis, and trending stories.'); ?>">
    <?php if(View::hasSection('og-image')): ?>
    <meta name="twitter:image" content="<?php echo $__env->yieldContent('og-image'); ?>">
    <meta name="twitter:image:alt" content="<?php echo $__env->yieldContent('og-image-alt', $siteName . ' — Featured Image'); ?>">
    <?php endif; ?>

    
    <?php echo $__env->yieldContent('head-extra'); ?>

    
    <?php if(\App\Models\Setting::get('rss_enabled', 'true') === 'true'): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo e(\App\Models\Setting::get('rss_title', 'Atomni News Feed')); ?>" href="<?php echo e(route('feed')); ?>">
    <?php endif; ?>

    <title><?php echo $__env->yieldContent('title', 'At Omni — News & Insights'); ?></title>

    
    <?php $favicon = \App\Models\Setting::get('site_favicon', ''); ?>
    <?php if($favicon && file_exists(public_path('storage/' . $favicon))): ?>
        <link rel="icon" href="<?php echo e(asset('storage/' . $favicon)); ?>?v=<?php echo e(@filemtime(public_path('storage/' . $favicon)) ?: time()); ?>">
        <link rel="shortcut icon" href="<?php echo e(asset('storage/' . $favicon)); ?>?v=<?php echo e(@filemtime(public_path('storage/' . $favicon)) ?: time()); ?>">
        <link rel="apple-touch-icon" href="<?php echo e(asset('storage/' . $favicon)); ?>?v=<?php echo e(@filemtime(public_path('storage/' . $favicon)) ?: time()); ?>">
    <?php else: ?>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📰</text></svg>">
        <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📰</text></svg>">
        <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📰</text></svg>">
    <?php endif; ?>

    
    <style>
        /* Prevent FOUC: match body bg instantly */
        html, body { background-color: #0A0E27; color: #E2E8F0; margin: 0; }
        html.light, html.light body { background-color: #FBFDFF; color: #334155; }
        /* Topbar skeleton */
        #topbar {
            position: fixed; inset-x: 0; top: 0; height: 30px; z-index: 55;
            background-color: #0F1535;
            border-bottom: 1px solid rgba(30,45,85,0.5);
        }
        html.light #topbar { background-color: #F1F5F9; }
        /* Header skeleton */
        #site-header {
            position: fixed; inset-x: 0; top: 30px; height: 64px; z-index: 50;
            background-color: rgba(10,14,39,0.8);
            border-bottom: 1px solid rgba(30,45,85,0.5);
            backdrop-filter: blur(20px);
        }
        html.light #site-header { background-color: rgba(255,255,255,0.8); }
        /* Hero placeholder — prevents layout shift while image loads */
        #featured-carousel { min-height: 320px; background-color: #162040; border-radius: 1rem; }
        /* Font stack fallback until Google Fonts loads */
        * { font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; }
    </style>

    

    <script>
        (function() {
            var t = localStorage.getItem('atomni-theme');
            if (t === 'light') document.documentElement.classList.add('light');
        })();
    </script>

    
    <?php
        $theme_type = \App\Models\Setting::get('theme_type', 'preset');
        $font_family = \App\Models\Setting::get('font_family', 'Inter');
        // Handle legacy font keys
        if ($font_family === 'inter') $font_family = 'Inter';
        if ($font_family === 'roboto') $font_family = 'Roboto';
        if ($font_family === 'dm_sans') $font_family = 'DM Sans';
        
        $encoded_font = str_replace(' ', '+', $font_family);
        $font_css = "'{$font_family}', sans-serif";
        
        $primary = \App\Models\Setting::get('theme_manual_primary', '#2D7FF9');
        $secondary = \App\Models\Setting::get('theme_manual_secondary', '#1A5FD1');
    ?>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <?php
        $storage_url = rtrim(config('filesystems.disks.public.url', ''), '/');
        $storage_host = parse_url($storage_url, PHP_URL_HOST);
    ?>
    <?php if($storage_host): ?>
    <link rel="dns-prefetch" href="//<?php echo e($storage_host); ?>">
    <?php endif; ?>



    
    
    <link id="app-font"
          rel="stylesheet"
          media="print"
          onload="this.media='all'"
          href="https://fonts.googleapis.com/css2?family=<?php echo e($encoded_font); ?>:wght@400;500;600;700;800&display=swap">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=<?php echo e($encoded_font); ?>:wght@400;500;600;700;800&display=swap">
    </noscript>

    <style>
        :root {
            --font-sans: <?php echo $font_css; ?> !important;
            --font-heading: <?php echo $font_css; ?> !important;
        }
        <?php if($theme_type === 'manual'): ?>
        :root {
            --color-electric: <?php echo e($primary); ?> !important;
            --color-accent-blue: <?php echo e($primary); ?> !important;
            --color-accent-blue-hover: <?php echo e($secondary); ?> !important;
            --color-cyan-glow: <?php echo e($secondary); ?> !important;
            --color-brand-primary: <?php echo e($primary); ?> !important;
            --color-brand-secondary: <?php echo e($secondary); ?> !important;
            --color-electric-dark: <?php echo e($secondary); ?> !important;
            --color-electric-light: color-mix(in srgb, <?php echo e($primary); ?> 70%, white) !important;
        }
        <?php endif; ?>

        /* ── Universal box model & overflow safety ── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Accessibility: Global focus states */
        *:focus-visible {
            outline: none !important;
            box-shadow: 0 0 0 2px var(--color-electric) !important;
        }
        
        /* Typography responsiveness */
        h1, h2, h3, h4, h5, h6 { word-break: break-word; }

        /* Hide scrollbar for nav containers */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    
    <?php if (! empty(trim($__env->yieldContent('lcp-preload')))): ?>
        <?php echo $__env->yieldContent('lcp-preload'); ?>
    <?php endif; ?>

    
    <?php $ga_id = \App\Models\Setting::get('ga_measurement_id', ''); ?>
    <?php if($ga_id): ?>
    
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    (function() {
        // Read any previously stored user preference
        var stored = null;
        try { stored = JSON.parse(localStorage.getItem('atomni-cookie-preference')); } catch(e) {}

        if (stored) {
            // Returning visitor — apply their saved consent immediately
            gtag('consent', 'default', {
                'ad_storage':          stored.marketing ? 'granted' : 'denied',
                'ad_user_data':        stored.marketing ? 'granted' : 'denied',
                'ad_personalization':  stored.marketing ? 'granted' : 'denied',
                'analytics_storage':   stored.analytics ? 'granted' : 'denied'
            });
        } else {
            // New visitor — deny all until the banner is interacted with
            gtag('consent', 'default', {
                'ad_storage':          'denied',
                'ad_user_data':        'denied',
                'ad_personalization':  'denied',
                'analytics_storage':   'denied',
                'wait_for_update':     500
            });
        }
        // Redact ad-click identifiers when ad_storage is denied (privacy best practice)
        gtag('set', 'ads_data_redaction', true);
        // Pass ad click info in URLs when cookies are denied (improves conversion tracking)
        gtag('set', 'url_passthrough', true);
    })();
    </script>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($ga_id); ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?php echo e($ga_id); ?>', { 'send_page_view': true });
    </script>
    <?php endif; ?>

    
    
    <?php $siteName = \App\Models\Setting::get('site_name', 'Atomni'); ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo e(e($siteName)); ?>",
        "url": "<?php echo e(url('/')); ?>",
        "description": "<?php echo e(e(\App\Models\Setting::get('website_tagline', 'Breaking News, Analysis & Trending Stories'))); ?>",
        "inLanguage": "en-IN",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?php echo e(url('/search')); ?>?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo e(e($siteName)); ?>",
        "alternateName": "Atomni News",
        "url": "<?php echo e(url('/')); ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo e(url('/')); ?>/favicon.ico",
            "width": 512,
            "height": 512
        },
        "description": "Atomni provides AI workflow automation and business process solutions for service businesses, agencies, and operators.",
        "foundingDate": "2024",
        "sameAs": [
            "https://twitter.com/atomni",
            "https://facebook.com/atomninews",
            "https://instagram.com/atomni",
            "https://linkedin.com/company/atomni"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "email": "contact@atomni.in",
            "contactType": "editorial",
            "areaServed": "IN",
            "availableLanguage": "English"
        }
    }
    </script>

    
    <?php echo $__env->yieldContent('schema'); ?>

    
    <?php $adsense_pub_id = \App\Models\Setting::get('adsense_pub_id', ''); ?>
    <?php if($adsense_pub_id): ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo e($adsense_pub_id); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>

    
    <?php $gtm_id = \App\Models\Setting::get('gtm_container_id', ''); ?>
    <?php if($gtm_id): ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo e($gtm_id); ?>');</script>
    
    <?php endif; ?>

</head>
<body class="bg-navy-950 text-text-primary font-sans antialiased min-h-screen transition-colors duration-300">

    
    <?php if($gtm_id): ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtm_id); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    
    <?php endif; ?>

    <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.ticker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>
        <?php $ad_header_leaderboard = \App\Models\Setting::get('ad_header_leaderboard', ''); ?>
        <?php if($ad_header_leaderboard): ?>
        <div class="w-full bg-navy-900 border-b border-navy-700/50 flex justify-center items-center py-4 mb-4">
            <div class="max-w-[728px] w-full text-center overflow-hidden">
                <?php echo $ad_header_leaderboard; ?>

            </div>
        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.cookie-consent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <button id="go-top-btn" aria-label="Go to Top" class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-electric text-white shadow-lg shadow-electric/30 hover:bg-electric-light hover:-translate-y-1 hover:shadow-electric/50 focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-950 transition-all duration-300 transform translate-y-20 opacity-0 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
    </button>

    
    <script>
        // ── Theme Toggle ──────────────────────────────────
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

            // Set correct icons on load
            applyIcons();

            if (btn) {
                btn.addEventListener('click', function() {
                    html.classList.toggle('light');
                    var isLight = html.classList.contains('light');
                    localStorage.setItem('atomni-theme', isLight ? 'light' : 'dark');
                    applyIcons();
                });
            }
        })();

        // ── Go to Top Button ───────────────────────────────
        (function() {
            var goTopBtn = document.getElementById('go-top-btn');
            if (goTopBtn) {
                // Show/hide based on scroll
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 400) {
                        goTopBtn.classList.remove('translate-y-20', 'opacity-0');
                        goTopBtn.classList.add('translate-y-0', 'opacity-100');
                    } else {
                        goTopBtn.classList.remove('translate-y-0', 'opacity-100');
                        goTopBtn.classList.add('translate-y-20', 'opacity-0');
                    }
                }, { passive: true });

                // Scroll to top on click
                goTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();

        // ── Search Autocomplete ────────────────────────────────────
        (function() {
            const searchContainers = document.querySelectorAll('.js-search-container');
            
            searchContainers.forEach(container => {
                const input = container.querySelector('.js-autocomplete-search');
                const dropdown = container.querySelector('.js-search-dropdown');
                const resultsList = container.querySelector('.js-search-results');
                const loader = container.querySelector('.js-search-loading');
                let debounceTimeout;

                if (!input || !dropdown || !resultsList) return;

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) {
                        closeDropdown();
                    }
                });

                input.addEventListener('focus', () => {
                    if (input.value.trim().length >= 2 && resultsList.children.length > 0) {
                        openDropdown();
                    }
                });

                input.addEventListener('input', (e) => {
                    const query = e.target.value.trim();
                    
                    clearTimeout(debounceTimeout);

                    if (query.length < 2) {
                        closeDropdown();
                        return;
                    }

                    openDropdown();
                    resultsList.innerHTML = '';
                    loader.classList.remove('hidden');

                    debounceTimeout = setTimeout(() => {
                        const apiUrl = `<?php echo e(url('/api/search/suggestions')); ?>?q=${encodeURIComponent(query)}`;
                        fetch(apiUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                loader.classList.add('hidden');
                                resultsList.innerHTML = '';
                                
                                if (data.length === 0) {
                                    resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-text-muted">No articles found.</li>';
                                    return;
                                }

                                data.forEach(item => {
                                    const li = document.createElement('li');
                                    li.innerHTML = `
                                        <a href="${item.url}" class="block px-4 py-3 hover:bg-navy-800 light:hover:bg-slate-50 transition-colors">
                                            <div class="text-sm font-medium text-text-primary line-clamp-1">${item.title}</div>
                                            <div class="text-xs text-text-muted mt-1">${item.date}</div>
                                        </a>
                                    `;
                                    resultsList.appendChild(li);
                                });
                            })
                            .catch(error => {
                                loader.classList.add('hidden');
                                resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-red-500">Error loading suggestions.</li>';
                            });
                    }, 300);
                });

                function openDropdown() {
                    dropdown.classList.remove('hidden');
                    // small delay for transition
                    setTimeout(() => dropdown.classList.remove('opacity-0'), 10);
                }

                function closeDropdown() {
                    dropdown.classList.add('opacity-0');
                    setTimeout(() => dropdown.classList.add('hidden'), 200);
                }
            });
        })();
    </script>

    

    <?php echo $__env->make('partials.adblock-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/layouts/app.blade.php ENDPATH**/ ?>