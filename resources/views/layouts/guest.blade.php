<!DOCTYPE html>
@php $theme_color = \App\Models\Setting::get('theme_color', 'blue'); @endphp
<html lang="en" class="scroll-smooth theme-{{ $theme_color }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to Atomni">

    <title>@yield('title', 'Login - Atomni')</title>

    {{-- Favicon --}}
    @php $favicon = \App\Models\Setting::get('site_favicon', ''); @endphp
    @if($favicon && file_exists(public_path('storage/' . $favicon)))
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📰</text></svg>">
    @endif

    <script>
        (function() {
            var t = localStorage.getItem('atomni-theme');
            if (t === 'light') document.documentElement.classList.add('light');
        })();
    </script>

    {{-- Dynamic Theme & Typography --}}
    @php
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
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link id="app-font" href="https://fonts.googleapis.com/css2?family={{ $encoded_font }}:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-sans: {!! $font_css !!} !important;
            --font-heading: {!! $font_css !!} !important;
        }
        @if($theme_type === 'manual')
        :root {
            --color-electric: {{ $primary }} !important;
            --color-accent-blue: {{ $primary }} !important;
            --color-accent-blue-hover: {{ $secondary }} !important;
            --color-cyan-glow: {{ $secondary }} !important;
            --color-brand-primary: {{ $primary }} !important;
            --color-brand-secondary: {{ $secondary }} !important;
            --color-electric-dark: {{ $secondary }} !important;
            --color-electric-light: color-mix(in srgb, {{ $primary }} 70%, white) !important;
        }
        @endif
        /* Base hidden states */
        img.logo-light, .logo-light { display: none !important; }
        img.logo-dark, .logo-dark  { display: none !important; }
        
        /* Dark Theme Roles (Default) */
        html:not(.light) img.logo-dark, html:not(.light) .logo-dark { display: block !important; }
        
        /* Light Theme Roles */
        html.light img.logo-light, html.light .logo-light { display: block !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-navy-950 text-text-primary font-sans antialiased min-h-screen transition-colors duration-300 flex flex-col">
    @yield('content')
    @include('partials.adblock-modal')
</body>
</html>
