@extends('admin.layouts.app')

@section('title', 'Global Settings')

@section('content')
<div class="mb-6 lg:mb-8">
    <h1 class="page-title text-2xl sm:text-3xl font-bold tracking-tight text-text-primary">Global Settings</h1>
    <p class="text-sm sm:text-base text-text-muted mt-1 sm:mt-2">Configure your website's core identity, visual theme, and integrations.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 items-start">
    
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-3 static lg:sticky top-[90px] z-30 mb-6 lg:mb-0">
        <div class="glass-card rounded-2xl p-3 flex flex-row lg:flex-col gap-2 overflow-x-auto hide-scrollbar">
            <a href="#section-identity" id="nav-identity" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap bg-accent-blue/10 text-accent-blue font-bold">
                <div class="w-8 h-8 rounded-lg bg-accent-blue/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10l2 2 4-4"></path></svg>
                </div>
                <div>
                    <div class="text-sm">Site Identity</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Logos, Ticker, Contact</div>
                </div>
            </a>
            <a href="#section-theme" id="nav-theme" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap text-text-muted hover:bg-navy-800 hover:text-text-primary font-medium">
                <div class="w-8 h-8 rounded-lg bg-navy-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.828 2.828a2 2 0 010 2.828l-8.486 8.486L11 21" /></svg>
                </div>
                <div>
                    <div class="text-sm">Theme Options</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Colors & Typography</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="lg:col-span-9 space-y-16 pb-12">
        
        <!-- === SITE IDENTITY section === -->
        <form id="section-identity" action="{{ route('admin.settings.global') }}" method="POST" class="scroll-mt-[100px]">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="identity">

            <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 -mt-4 border-b border-navy-700/50">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-accent-blue/10 text-accent-blue hidden sm:flex shrink-0 items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10l2 2 4-4"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">Site Identity</h2>
                        <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Configure your website's name and branding.</p>
                    </div>
                </div>
                <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-sm sm:text-base">
                    Save <span class="hidden sm:inline">Identity</span>
                </button>
            </div>

            <div class="grid grid-cols-12 gap-5 lg:gap-6">
                
                <!-- Basic Info Card -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-5 xl:col-span-4 space-y-6 h-fit">
                    <h3 class="text-sm font-bold text-text-primary border-b border-navy-700/30 pb-4 mb-4">Basic Information</h3>
                    
                    <!-- Site Name -->
                    <div class="space-y-2">
                        <label for="site_name" class="block text-sm font-bold text-text-secondary">Website Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium">
                        @error('site_name')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website Tagline -->
                    <div class="space-y-2">
                        <label for="website_tagline" class="block text-sm font-bold text-text-secondary">Website Tagline</label>
                        <input type="text" name="website_tagline" id="website_tagline" value="{{ old('website_tagline', $settings['website_tagline'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium" placeholder="E.g., Your source for daily news">
                        @error('website_tagline')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Branding Logos Card -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-7 xl:col-span-8 h-fit">
                    <h3 class="text-sm font-bold text-text-primary border-b border-navy-700/30 pb-4 mb-6">Logos & Iconography</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Site Logo (Light Mode) -->
                        <div class="space-y-4">
                            <div class="block text-sm font-bold text-text-secondary flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Light Logo
                            </div>
                            <div class="w-full h-32 shrink-0 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden p-4 relative group {{ empty($settings['site_logo']) || !file_exists(public_path('storage/' . $settings['site_logo'])) ? 'hidden' : '' }}" id="logo_preview_container">
                                <img loading="lazy" src="{{ !empty($settings['site_logo']) && file_exists(public_path('storage/' . $settings['site_logo'])) ? \Illuminate\Support\Facades\Storage::url($settings['site_logo']) : '#' }}" alt="Site Logo Light Preview" id="logo_preview_image" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform">
                            </div>
                            <div class="relative">
                                <input type="hidden" name="site_logo" id="site_logo" value="{{ $settings['site_logo'] ?? '' }}">
                                <button type="button" onclick="openMediaSelector({onSelect: (media) => { document.getElementById('site_logo').value = media.file_path; document.getElementById('logo_preview_image').src = media.url; document.getElementById('logo_preview_container').classList.remove('hidden'); }})" class="btn-primary w-full justify-center">
                                    Select Light Logo
                                </button>
                                <p class="mt-3 text-[11px] text-text-muted font-medium leading-relaxed">
                                    <strong class="text-text-secondary uppercase tracking-wider text-[10px]">Specs:</strong><br>
                                    PNG or SVG (Transparent)<br>
                                    250x50px • Max 2MB
                                </p>
                            </div>
                            @error('site_logo')
                                <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Site Logo (Dark Mode) -->
                        <div class="space-y-4">
                            <div class="block text-sm font-bold text-text-secondary flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                Dark Logo
                            </div>
                            @php
                                $hasDarkLogo = !empty($settings['site_logo_dark']) && file_exists(public_path('storage/' . $settings['site_logo_dark']));
                                $hasLightLogo = !empty($settings['site_logo']) && file_exists(public_path('storage/' . $settings['site_logo']));
                                $darkPreviewUrl = $hasDarkLogo ? \Illuminate\Support\Facades\Storage::url($settings['site_logo_dark']) : ($hasLightLogo ? \Illuminate\Support\Facades\Storage::url($settings['site_logo']) : '#');
                                $hideDarkPreview = !$hasDarkLogo && !$hasLightLogo;
                            @endphp
                            <div class="w-full h-32 shrink-0 rounded-xl bg-navy-950/60 border border-navy-700/30 flex items-center justify-center overflow-hidden p-4 relative group {{ $hideDarkPreview ? 'hidden' : '' }}" id="logo_dark_preview_container">
                                <img loading="lazy" src="{{ $darkPreviewUrl }}" alt="Site Logo Dark Preview" id="logo_dark_preview_image" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform">
                            </div>
                            <div class="relative">
                                <input type="hidden" name="site_logo_dark" id="site_logo_dark" value="{{ $settings['site_logo_dark'] ?? '' }}">
                                <button type="button" onclick="openMediaSelector({onSelect: (media) => { document.getElementById('site_logo_dark').value = media.file_path; document.getElementById('logo_dark_preview_image').src = media.url; document.getElementById('logo_dark_preview_container').classList.remove('hidden'); }})" class="btn-primary w-full justify-center">
                                    Select Dark Logo
                                </button>
                                <p class="mt-3 text-[11px] text-text-muted font-medium leading-relaxed">
                                    <strong class="text-text-secondary uppercase tracking-wider text-[10px]">Specs:</strong><br>
                                    PNG or SVG (Transparent)<br>
                                    250x50px • Max 2MB
                                </p>
                            </div>
                            @error('site_logo_dark')
                                <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Site Favicon -->
                        <div class="space-y-4">
                            <div class="block text-sm font-bold text-text-secondary flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                Site Favicon
                            </div>
                            <div class="w-32 h-32 shrink-0 rounded-xl bg-navy-950/60 border border-navy-700/30 flex items-center justify-center overflow-hidden p-3 relative group {{ empty($settings['site_favicon']) || !file_exists(public_path('storage/' . $settings['site_favicon'])) ? 'hidden' : 'mx-auto' }}" id="favicon_preview_container">
                                <img loading="lazy" src="{{ !empty($settings['site_favicon']) && file_exists(public_path('storage/' . $settings['site_favicon'])) ? \Illuminate\Support\Facades\Storage::url($settings['site_favicon']) : '#' }}" alt="Favicon Preview" id="favicon_preview_image" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform">
                            </div>
                            <div class="relative">
                                <input type="hidden" name="site_favicon" id="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}">
                                <button type="button" onclick="openMediaSelector({onSelect: (media) => { document.getElementById('site_favicon').value = media.file_path; document.getElementById('favicon_preview_image').src = media.url; document.getElementById('favicon_preview_container').classList.remove('hidden'); }})" class="btn-primary w-full justify-center">
                                    Select Favicon
                                </button>
                                <p class="mt-3 text-[11px] text-text-muted font-medium leading-relaxed">
                                    <strong class="text-text-secondary uppercase tracking-wider text-[10px]">Specs:</strong><br>
                                    ICO, PNG, or SVG (Square)<br>
                                    512x512px • Max 1MB
                                </p>
                            </div>
                            @error('site_favicon')
                                <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Breaking News Ticker Control -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-8 h-fit">
                    <div class="flex items-center gap-2 mb-6 border-b border-navy-700/30 pb-4">
                        <svg class="w-5 h-5 text-alert-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                        <h3 class="text-sm font-bold text-text-primary">Breaking News Ticker</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <!-- Ticker Toggle -->
                            <div class="flex items-center justify-between p-4 rounded-xl bg-navy-950/40 border border-navy-700/30">
                                <div>
                                    <span class="block text-sm font-bold text-text-primary">Enable Live Ticker</span>
                                    <span class="block text-[11px] font-medium text-text-muted mt-0.5">Show a scrolling news bar at the top of the website.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" aria-label="Enable Live Ticker" name="ticker_enabled" value="1" {{ old('ticker_enabled', $settings['ticker_enabled']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-navy-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent-blue"></div>
                                </label>
                            </div>
                            
                            <!-- Ticker Speed Control -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="ticker_speed" class="block text-sm font-bold text-text-secondary">Scroll Speed Duration</label>
                                    <span class="text-xs font-bold text-electric px-2 py-1 rounded bg-electric/10"><span id="speed_display">{{ old('ticker_speed', $settings['ticker_speed'] ?? '25') }}</span>s</span>
                                </div>
                                <p class="text-[11px] text-text-muted mb-3 font-medium">Lower numbers mean a faster scroll since it represents the time taken to complete one exact loop.</p>
                                <input type="range" name="ticker_speed" id="ticker_speed" min="5" max="120" step="1" value="{{ old('ticker_speed', $settings['ticker_speed'] ?? '25') }}" class="w-full h-2 bg-navy-700 rounded-lg appearance-none cursor-pointer accent-accent-blue" oninput="document.getElementById('speed_display').innerText = this.value">
                            </div>
                        </div>

                        <!-- Ticker Text Mode -->
                        <div class="space-y-4 flex flex-col justify-between">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:h-[90px]">
                                <label class="relative cursor-pointer group h-full">
                                    <input type="radio" aria-label="Ticker Mode: Latest Posts" name="ticker_mode" value="latest_posts" {{ old('ticker_mode', $settings['ticker_mode'] ?? 'latest_posts') == 'latest_posts' ? 'checked' : '' }} class="peer sr-only" onclick="document.getElementById('ticker_text_container').classList.add('hidden')">
                                    <div class="p-4 rounded-xl bg-navy-950/40 border-2 border-transparent peer-checked:border-accent-blue peer-checked:bg-navy-900/60 transition-all hover:bg-navy-900/40 shadow-sm flex flex-col justify-center h-full">
                                        <span class="text-sm font-bold text-text-primary mb-1">Latest Posts</span>
                                        <span class="text-[11px] font-medium text-text-muted">Show 5 recent headlines</span>
                                    </div>
                                    <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-accent-blue text-white">
                                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group h-full">
                                    <input type="radio" aria-label="Ticker Mode: Custom Announcement" name="ticker_mode" value="custom_announcement" {{ old('ticker_mode', $settings['ticker_mode'] ?? 'latest_posts') == 'custom_announcement' ? 'checked' : '' }} class="peer sr-only" onclick="document.getElementById('ticker_text_container').classList.remove('hidden')">
                                    <div class="p-4 rounded-xl bg-navy-950/40 border-2 border-transparent peer-checked:border-accent-blue peer-checked:bg-navy-900/60 transition-all hover:bg-navy-900/40 shadow-sm flex flex-col justify-center h-full">
                                        <span class="text-sm font-bold text-text-primary mb-1">Custom Message</span>
                                        <span class="text-[11px] font-medium text-text-muted">Show fixed message</span>
                                    </div>
                                    <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-accent-blue text-white">
                                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <div id="ticker_text_container" class="flex-1 mt-4 {{ old('ticker_mode', $settings['ticker_mode'] ?? 'latest_posts') == 'latest_posts' ? 'hidden' : '' }}">
                                <label for="ticker_text" class="block text-sm font-bold text-text-secondary mb-2">Ticker Headlines (One per line)</label>
                                <textarea name="ticker_text" id="ticker_text" rows="3" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium resize-y min-h-[90px] sm:h-[90px]" placeholder="⚡ Supreme Court delivers landmark ruling...&#10;📈 Global markets rally...">{{ old('ticker_text', $settings['ticker_text'] ?? '') }}</textarea>
                                <p class="text-[11px] text-text-muted mt-2 leading-relaxed">Each line will be separated by a spacer in the scrolling banner.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Library -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-4 h-fit">
                        <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <h3 class="text-sm font-bold text-text-primary">Media Library</h3>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="pr-4">
                                <span class="block text-sm font-bold text-text-primary">Keep Originals</span>
                                <span class="block text-[11px] font-medium text-text-muted mt-0.5">Preserve original image alongside WebP derivatives.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" aria-label="Keep Original Media" name="media_keep_original" value="1" {{ old('media_keep_original', $settings['media_keep_original'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-navy-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent-blue"></div>
                            </label>
                        </div>
                    </div>

                <!-- Donate Settings -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-8 h-fit">
                    <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                        <svg class="w-5 h-5 text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                        <h3 class="text-sm font-bold text-text-primary">Donate / Contribution</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label for="donation_link" class="block text-sm font-bold text-text-secondary mb-2">Donate Button Link</label>
                                <input type="url" name="donation_link" id="donation_link" value="{{ old('donation_link', $settings['donation_link'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium text-sm" placeholder="https://yourdomain.com/donate">
                            </div>
                            <div>
                                <label for="donate_qr_link" class="block text-sm font-bold text-text-secondary mb-2">UPI / Payment Link</label>
                                <input type="text" name="donate_qr_link" id="donate_qr_link" value="{{ old('donate_qr_link', $settings['donate_qr_link'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium text-sm" placeholder="upi://pay?...">
                            </div>
                        </div>
                        <div class="h-full">
                            <label for="donate_note" class="block text-sm font-bold text-text-secondary mb-2">Donation Note (optional)</label>
                            <textarea name="donate_note" id="donate_note" class="block w-full h-[120px] px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium resize-none text-sm" placeholder="Short note...">{{ old('donate_note', $settings['donate_note'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-4 h-fit">
                    <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                            <svg class="w-5 h-5 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <h3 class="text-sm font-bold text-text-primary">Contact Info</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="contact_email" class="block text-sm font-bold text-text-secondary mb-2">Support Email</label>
                                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium text-sm" placeholder="hello@atomni.in">
                            </div>
                            <div>
                                <label for="contact_phone" class="block text-sm font-bold text-text-secondary mb-2">Support Phone</label>
                                <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium text-sm" placeholder="+1 (555) 123-4567">
                            </div>
                            <div>
                                <label for="contact_address" class="block text-sm font-bold text-text-secondary mb-2">Office Address</label>
                                <textarea name="contact_address" id="contact_address" rows="2" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium resize-y text-sm" placeholder="123 Atomni Street...">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="contact_map_embed" class="block text-sm font-bold text-text-secondary mb-2">Maps iframe Embed</label>
                                <textarea name="contact_map_embed" id="contact_map_embed" rows="2" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium resize-y font-mono text-[10px]" placeholder='<iframe src="..."></iframe>'>{{ old('contact_map_embed', $settings['contact_map_embed'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                <!-- Homepage Features -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 h-fit mt-6">
                    <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <h3 class="text-sm font-bold text-text-primary">Homepage Features</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Enable Section -->
                        <div class="flex items-center justify-between p-4 rounded-xl bg-navy-950/40 border border-navy-700/30 lg:col-span-1">
                            <div>
                                <span class="block text-sm font-bold text-text-primary">Selected Author Section</span>
                                <span class="block text-[11px] font-medium text-text-muted mt-0.5">Show a carousel of articles from a specific author above Editor's Picks.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-4">
                                <input type="checkbox" aria-label="Enable Selected Author Section" name="home_author_section_enabled" value="1" {{ old('home_author_section_enabled', $settings['home_author_section_enabled']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-navy-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent-blue"></div>
                            </label>
                        </div>

                        <!-- Section Title -->
                        <div class="lg:col-span-1 space-y-2">
                            <label for="home_author_section_title" class="block text-sm font-bold text-text-secondary">Section Title</label>
                            <input type="text" name="home_author_section_title" id="home_author_section_title" value="{{ old('home_author_section_title', $settings['home_author_section_title'] ?? '') }}" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium" placeholder="E.g., TOI Young Leaders">
                            @error('home_author_section_title')
                                <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Author Selection -->
                        <div class="lg:col-span-1 space-y-2">
                            <label for="home_author_section_author_id" class="block text-sm font-bold text-text-secondary">Select Author</label>
                            <select name="home_author_section_author_id" id="home_author_section_author_id" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium appearance-none cursor-pointer">
                                <option value="">-- Choose an Author --</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ old('home_author_section_author_id', $settings['home_author_section_author_id']) == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }} ({{ ucfirst(str_replace('_', ' ', $author->role)) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('home_author_section_author_id')
                                <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Ensure the script is included at the end of the form -->
                <script>
                    function previewLogo(event, imgId, containerId) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewImage = document.getElementById(imgId);
                                const previewContainer = document.getElementById(containerId);
                                previewImage.src = e.target.result;
                                previewContainer.classList.remove('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    }
                </script>
            </div>
        </form>

        <!-- === THEME CUSTOMIZATION section === -->
        <form id="section-theme" action="{{ route('admin.settings.global') }}" method="POST" class="scroll-mt-[100px]">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="theme">
            
            <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 mt-8 border-b border-navy-700/50">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl hidden sm:flex shrink-0 items-center justify-center"
                         style="background: color-mix(in srgb, var(--color-electric) 10%, transparent); color: var(--color-electric);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.828 2.828a2 2 0 010 2.828l-8.486 8.486L11 21" /></svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">Theme Options</h2>
                        <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Customize your publication's visual identity, colors, and typography.</p>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="submit" name="action" value="reset" class="btn-primary !bg-navy-800 hover:!bg-navy-700 !text-text-secondary px-3 py-2 text-sm sm:px-4 sm:py-2.5 sm:text-base" onclick="return confirm('Are you sure you want to reset all theme settings to their defaults?');">
                        <span class="hidden sm:inline">Reset Defaults</span>
                        <span class="sm:hidden">Reset</span>
                    </button>
                    <button type="submit" name="action" value="save" class="btn-primary px-4 py-2 text-sm sm:px-4 sm:py-2.5 sm:text-base">
                        Save <span class="hidden sm:inline">Theme</span>
                    </button>
                </div>
            </div>


            <div class="grid grid-cols-12 gap-5 lg:gap-6 items-start">
                <!-- Theme Type & Colors Card -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-8 space-y-8 h-fit">
                    <!-- Theme Mode Toggle -->
                    <div>
                        <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            <h3 class="text-sm font-bold text-text-primary">Theme Type</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer group">
                                <input type="radio" aria-label="Theme Type: Curated Presets" name="theme_type" value="preset" {{ old('theme_type', $settings['theme_type'] ?? 'preset') == 'preset' ? 'checked' : '' }} class="peer sr-only" onclick="document.getElementById('preset_themes').style.display='block'; document.getElementById('manual_themes').style.display='none';">
                                <div class="p-4 text-center rounded-2xl bg-navy-950/40 border-2 border-transparent peer-checked:border-accent-blue peer-checked:bg-navy-900/60 transition-all hover:bg-navy-900/40 shadow-sm">
                                    <span class="text-sm font-bold text-text-muted peer-checked:text-text-primary">Curated Presets</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input type="radio" aria-label="Theme Type: Manual Custom Colors" name="theme_type" value="manual" {{ old('theme_type', $settings['theme_type'] ?? 'preset') == 'manual' ? 'checked' : '' }} class="peer sr-only" onclick="document.getElementById('preset_themes').style.display='none'; document.getElementById('manual_themes').style.display='block';">
                                <div class="p-4 text-center rounded-2xl bg-navy-950/40 border-2 border-transparent peer-checked:border-accent-blue peer-checked:bg-navy-900/60 transition-all hover:bg-navy-900/40 shadow-sm">
                                    <span class="text-sm font-bold text-text-muted peer-checked:text-text-primary">Manual Custom Colors</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Curated Presets Panel -->
                    <div id="preset_themes" style="display: {{ old('theme_type', $settings['theme_type'] ?? 'preset') == 'preset' ? 'block' : 'none' }}">
                        <div class="flex items-center gap-2 mb-4 border-b border-navy-700/30 pb-4">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.828 2.828a2 2 0 010 2.828l-8.486 8.486L11 21" /></svg>
                            <h3 class="text-sm font-bold text-text-primary">Color Scheme</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach($curatedPresets as $value => $data)
                                <label class="relative cursor-pointer group block">
                                    <input type="radio" aria-label="Theme Color: {{ $data['label'] }}" name="theme_color" value="{{ $value }}" {{ old('theme_color', $settings['theme_color'] ?? 'blue') == $value ? 'checked' : '' }} class="peer sr-only">
                                    <div class="p-5 rounded-2xl bg-navy-950/40 border border-navy-700/30 peer-checked:border-accent-blue peer-checked:bg-navy-900/60 transition-all hover:bg-navy-900/40 shadow-sm h-full flex flex-col justify-between">
                                        <div>
                                            <!-- Color chips -->
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-8 h-8 rounded-lg shadow-md border border-navy-700/30 group-hover:scale-105 transition-transform" style="background: {{ $data['primary'] }}"></div>
                                                <div class="w-8 h-8 rounded-lg shadow-md border border-navy-700/30 group-hover:scale-105 transition-transform" style="background: {{ $data['secondary'] }}"></div>
                                                @if(isset($data['glow']))
                                                    <div class="w-8 h-8 rounded-lg shadow-md border border-navy-700/30 group-hover:scale-105 transition-transform" style="background: {{ $data['glow'] }}"></div>
                                                @endif
                                            </div>

                                            <h4 class="text-sm font-bold text-text-secondary group-hover:text-text-primary transition-colors">{{ $data['label'] }}</h4>
                                            <p class="text-xs text-text-muted mt-1 leading-relaxed">{{ $data['desc'] }}</p>
                                        </div>

                                        <!-- WCAG Access and status indicators -->
                                        <div class="mt-4 pt-3 border-t border-navy-700/20 flex items-center justify-between">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $data['contrast_class'] }}">
                                                {{ $data['accessibility'] }}
                                            </span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted peer-checked:text-accent-blue transition-colors">
                                                Selected
                                            </span>
                                        </div>
                                    </div>
                                    <div class="absolute -top-1 -right-1 opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-accent-blue text-white ring-4 ring-navy-950">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Manual Custom Colors Panel -->
                    <div id="manual_themes" style="display: {{ old('theme_type', $settings['theme_type'] ?? 'preset') == 'manual' ? 'block' : 'none' }}">
                        <div class="flex items-center gap-2 mb-6 border-b border-navy-700/30 pb-4">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.828 2.828a2 2 0 010 2.828l-8.486 8.486L11 21" /></svg>
                            <h3 class="text-sm font-bold text-text-primary font-heading">Manual Custom Theme Studio</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            <!-- Left controls: col-span-7 -->
                            <div class="lg:col-span-7 space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <!-- Primary Color Picker -->
                                    <div class="space-y-2">
                                        <label for="theme_manual_primary" class="block text-xs font-bold text-text-secondary uppercase tracking-wider">Primary Color</label>
                                        <div class="flex items-center gap-3">
                                            <input type="color" id="theme_manual_primary" name="theme_manual_primary" value="{{ old('theme_manual_primary', $settings['theme_manual_primary'] ?? '#2D7FF9') }}" class="h-11 w-11 rounded bg-transparent cursor-pointer border-0 p-0 shrink-0">
                                            <input type="text" aria-label="Primary Color Hex" id="theme_manual_primary_text" value="{{ old('theme_manual_primary', $settings['theme_manual_primary'] ?? '#2D7FF9') }}" class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono uppercase text-xs">
                                        </div>
                                        @error('theme_manual_primary')
                                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Secondary Color Picker -->
                                    <div class="space-y-2">
                                        <label for="theme_manual_secondary" class="block text-xs font-bold text-text-secondary uppercase tracking-wider">Secondary Color</label>
                                        <div class="flex items-center gap-3">
                                            <input type="color" id="theme_manual_secondary" name="theme_manual_secondary" value="{{ old('theme_manual_secondary', $settings['theme_manual_secondary'] ?? '#1A5FD1') }}" class="h-11 w-11 rounded bg-transparent cursor-pointer border-0 p-0 shrink-0">
                                            <input type="text" aria-label="Secondary Color Hex" id="theme_manual_secondary_text" value="{{ old('theme_manual_secondary', $settings['theme_manual_secondary'] ?? '#1A5FD1') }}" class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono uppercase text-xs">
                                        </div>
                                        @error('theme_manual_secondary')
                                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Color Pair Suggester / Generator -->
                                <div class="space-y-2">
                                    <span class="block text-xs font-bold text-text-muted uppercase tracking-wider">Palette Generators</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" onclick="suggestSecondary('dark')" class="px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 text-xs font-bold text-text-secondary transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                            Dark Gradient Shade
                                        </button>
                                        <button type="button" onclick="suggestSecondary('glow')" class="px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 text-xs font-bold text-text-secondary transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Glow Highlight
                                        </button>
                                        <button type="button" onclick="suggestSecondary('complementary')" class="px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 text-xs font-bold text-text-secondary transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            Complementary Color
                                        </button>
                                        <button type="button" onclick="randomizePair()" class="px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 text-xs font-bold text-text-secondary transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"/></svg>
                                            Randomize Combo
                                        </button>
                                    </div>
                                </div>

                                <!-- Quick Preset Palettes -->
                                <div class="space-y-3">
                                    <span class="block text-xs font-bold text-text-muted uppercase tracking-wider">Curated Color Pairs</span>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        <!-- Preset 1 -->
                                        <button type="button" onclick="applyManualPreset('#2563EB', '#8B5CF6')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#2563EB]"></span>
                                                <span class="w-1/2 h-full bg-[#8B5CF6]"></span>
                                            </span>
                                            Neon Sapphire
                                        </button>
                                        <!-- Preset 2 -->
                                        <button type="button" onclick="applyManualPreset('#F97316', '#EF4444')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#F97316]"></span>
                                                <span class="w-1/2 h-full bg-[#EF4444]"></span>
                                            </span>
                                            Sunset Ignite
                                        </button>
                                        <!-- Preset 3 -->
                                        <button type="button" onclick="applyManualPreset('#10B981', '#065F46')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#10B981]"></span>
                                                <span class="w-1/2 h-full bg-[#065F46]"></span>
                                            </span>
                                            Emerald Glow
                                        </button>
                                        <!-- Preset 4 -->
                                        <button type="button" onclick="applyManualPreset('#EC4899', '#14B8A6')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#EC4899]"></span>
                                                <span class="w-1/2 h-full bg-[#14B8A6]"></span>
                                            </span>
                                            Cyber Pulse
                                        </button>
                                        <!-- Preset 5 -->
                                        <button type="button" onclick="applyManualPreset('#84CC16', '#065F46')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#84CC16]"></span>
                                                <span class="w-1/2 h-full bg-[#065F46]"></span>
                                            </span>
                                            Toxic Lime
                                        </button>
                                        <!-- Preset 6 -->
                                        <button type="button" onclick="applyManualPreset('#F43F5E', '#475569')" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-navy-950/30 border border-navy-700/20 hover:border-navy-600 hover:bg-navy-900/40 transition-all text-xs font-bold text-text-secondary justify-start">
                                            <span class="w-4 h-4 rounded-full flex overflow-hidden border border-navy-800 shrink-0">
                                                <span class="w-1/2 h-full bg-[#F43F5E]"></span>
                                                <span class="w-1/2 h-full bg-[#475569]"></span>
                                            </span>
                                            Coral Slate
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Right preview & validation: col-span-5 -->
                            <div class="lg:col-span-5 space-y-6">
                                <!-- Mockup Preview Card -->
                                <div class="glass-card rounded-2xl p-4 bg-navy-900/30 border border-navy-800/80 space-y-4">
                                    <span class="block text-[11px] font-bold text-text-muted uppercase tracking-wider">Live Mockup Preview</span>
                                    
                                    <!-- Mockup Header -->
                                    <div id="mock-header" class="relative rounded-xl h-20 overflow-hidden flex items-center px-4 transition-all duration-300 bg-navy-950">
                                        <div id="mock-header-gradient" class="absolute inset-0 bg-gradient-to-br transition-all duration-300 opacity-90"></div>
                                        <div class="relative z-10 flex items-center justify-between w-full">
                                            <span class="text-xs font-black text-white tracking-widest font-heading">ATOMNI</span>
                                            <span id="mock-badge" class="px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest transition-all duration-300">Tech News</span>
                                        </div>
                                    </div>

                                    <!-- Mockup Components -->
                                    <div class="grid grid-cols-2 gap-4 pt-1">
                                        <!-- Primary Button Mockup -->
                                        <div class="space-y-1.5 flex flex-col justify-center">
                                            <span class="block text-[10px] font-bold text-text-muted uppercase tracking-wider">Button Hover State</span>
                                            <button id="mock-btn" type="button" class="w-full py-2.5 rounded-xl text-[11px] font-bold text-white transition-all transform hover:scale-[1.02] border-0 outline-none select-none">
                                                Button Action
                                            </button>
                                        </div>

                                        <!-- Form Input Accent Mockup -->
                                        <div class="space-y-1.5 flex flex-col justify-center">
                                            <span class="block text-[10px] font-bold text-text-muted uppercase tracking-wider">Accent Active Input</span>
                                            <div class="relative">
                                                <input type="text" aria-label="Mock Input" readonly value="Input text..." id="mock-input" class="w-full bg-navy-800/85 text-[11px] px-3 py-2 rounded-lg border focus:outline-none transition-all cursor-default select-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contrast Validation Card -->
                                <div class="glass-card rounded-2xl p-4 bg-navy-900/30 border border-navy-800/80 space-y-3">
                                    <span class="block text-[11px] font-bold text-text-muted uppercase tracking-wider">WCAG Accessibility Contrast</span>
                                    
                                    <div class="space-y-2.5">
                                        <!-- Contrast white text on primary -->
                                        <div class="flex items-center justify-between bg-navy-950/30 p-3 rounded-xl border border-navy-800/40">
                                            <div class="space-y-0.5">
                                                <span class="block text-xs font-bold text-text-primary leading-none">White Text on Button</span>
                                                <span class="text-[10px] text-text-muted">#FFFFFF on Primary Color</span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span id="contrast-btn-ratio" class="text-xs font-mono font-bold">4.5:1</span>
                                                <span id="contrast-btn-status" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase">PASS AA</span>
                                            </div>
                                        </div>

                                        <!-- Contrast primary on dark background -->
                                        <div class="flex items-center justify-between bg-navy-950/30 p-3 rounded-xl border border-navy-800/40">
                                            <div class="space-y-0.5">
                                                <span class="block text-xs font-bold text-text-primary leading-none">Primary on Dark BG</span>
                                                <span class="text-[10px] text-text-muted">Primary Color on #0A0E27</span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span id="contrast-dark-ratio" class="text-xs font-mono font-bold">4.5:1</span>
                                                <span id="contrast-dark-status" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase">PASS AA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography Card -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 col-span-12 lg:col-span-4 space-y-6 flex flex-col h-fit">
                    <div class="flex items-center gap-2 mb-2 border-b border-navy-700/30 pb-4">
                        <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-8m0 0l4 8m-4-8v12"/></svg>
                        <h3 class="text-sm font-bold text-text-primary">Typography</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="font_family" class="block text-sm font-bold text-text-secondary">Primary Font Family</label>
                        <select name="font_family" id="font_family" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent font-medium text-sm" onchange="updateFontPreview(this.value)">
                            @foreach([
                                'Inter' => 'Inter (Modern & Clean)',
                                'Roboto' => 'Roboto (Classic & Readable)',
                                'DM Sans' => 'DM Sans (Geometric & Friendly)',
                                'Poppins' => 'Poppins (Round & Elegant)',
                                'Montserrat' => 'Montserrat (Strong & Distinctive)',
                                'Open Sans' => 'Open Sans (Neutral & Professional)',
                                'Lato' => 'Lato (Warm & Balanced)',
                                'Playfair Display' => 'Playfair Display (Serif Classic)',
                            ] as $value => $label)
                                <option value="{{ $value }}" {{ old('font_family', $settings['font_family'] ?? 'Inter') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-text-muted mt-2">These fonts are automatically loaded from the Google Fonts library.</p>
                        @error('font_family')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex-1 flex flex-col">
                        <div class="block text-sm font-bold text-text-secondary mb-2 mt-4">Live Font Preview</div>
                        <div class="flex-1 p-6 rounded-xl bg-navy-950/60 border border-navy-700/30 min-h-[120px] flex flex-col justify-center text-center">
                            <h4 id="font_preview_heading" class="text-xl sm:text-3xl font-bold text-text-primary mb-2">The quick brown fox</h4>
                            <p id="font_preview_body" class="text-sm text-text-muted mt-2 leading-relaxed">Jumps over the lazy dog. 1234567890 !@#$%^&*()</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('admin-assets/js/theme-customizer.js') }}"></script>
@endsection