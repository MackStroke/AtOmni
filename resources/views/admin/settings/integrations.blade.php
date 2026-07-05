@extends('admin.layouts.app')

@section('title', 'External Integrations')

@section('content')
<div class="mb-6 lg:mb-8">
    <h1 class="page-title text-2xl sm:text-3xl font-bold tracking-tight text-text-primary">External Integrations</h1>
    <p class="text-sm sm:text-base text-text-muted mt-1 sm:mt-2">Connect third-party tools, services, and analytics offered by Google and others.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 items-start">
    
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-3 static lg:sticky top-[90px] z-30 mb-6 lg:mb-0">
        <div class="glass-card rounded-2xl p-3 flex flex-row lg:flex-col gap-2 overflow-x-auto hide-scrollbar">
            <a href="#section-tag-platform" id="nav-tag-platform" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap bg-accent-blue/10 text-accent-blue font-bold">
                <div class="icon-box w-8 h-8 rounded-lg bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <div>
                    <div class="text-sm">Tag Platform</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Analytics & GTM</div>
                </div>
            </a>
            <a href="#section-llm" id="nav-llm" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap text-text-muted hover:bg-navy-800 hover:text-text-primary font-medium">
                <div class="icon-box w-8 h-8 rounded-lg bg-navy-800 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div>
                    <div class="text-sm">AI & LLMs</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Gemini, OpenAI</div>
                </div>
            </a>
            <a href="#section-adsense" id="nav-adsense" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap text-text-muted hover:bg-navy-800 hover:text-text-primary font-medium">
                <div class="icon-box w-8 h-8 rounded-lg bg-navy-800 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <div class="text-sm">AdSense</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Monetization</div>
                </div>
            </a>
            <a href="#section-ga4" id="nav-ga4" class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all whitespace-nowrap text-text-muted hover:bg-navy-800 hover:text-text-primary font-medium">
                <div class="icon-box w-8 h-8 rounded-lg bg-navy-800 text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <div class="text-sm">GA4 Data API</div>
                    <div class="text-[10px] font-normal opacity-70 hidden lg:block">Reports Dashboard</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="lg:col-span-9 space-y-16 pb-12">
        <form action="{{ route('admin.settings.integrations.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- SECTION: Tag Platform -->
            <div id="section-tag-platform" class="scroll-mt-[100px] section-block">
                <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 -mt-4 border-b border-navy-700/50">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 hidden sm:flex shrink-0 items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">Google Tag Platform</h2>
                            <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Analytics, Tag Manager and Consent Mode v2.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-sm sm:text-base">
                        Save <span class="hidden sm:inline">Integrations</span>
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-5 sm:p-6 space-y-8">
                    {{-- Google Analytics ID --}}
                    <div class="space-y-3">
                        <label for="ga_measurement_id" class="block text-sm font-bold text-text-secondary">Google Analytics Measurement ID</label>
                        <input type="text" name="ga_measurement_id" id="ga_measurement_id"
                               value="{{ old('ga_measurement_id', $settings['ga_measurement_id'] ?? '') }}"
                               placeholder="G-XXXXXXXXXX"
                               class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium max-w-sm">

                        <div class="bg-navy-900/50 border border-accent-blue/20 rounded-xl p-4 mt-2">
                            <h4 class="text-xs font-bold text-accent-blue uppercase tracking-wider mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                How to find your Measurement ID
                            </h4>
                            <ol class="list-decimal list-inside text-xs text-text-muted space-y-1.5 ml-1">
                                <li>Sign in to your <a href="https://analytics.google.com" target="_blank" class="text-accent-blue hover:underline">Google Analytics account</a>.</li>
                                <li>Click on <strong>Admin</strong> (gear icon bottom left).</li>
                                <li>Under the Property column, click <strong>Data Streams</strong>.</li>
                                <li>Select your web data stream.</li>
                                <li>Your Measurement ID starts with <strong>"G-"</strong> (e.g., G-12345ABCD).</li>
                            </ol>
                            <p class="text-[10px] text-text-secondary mt-3 italic border-t border-navy-700/30 pt-2">Once saved, the tracking script is automatically injected with Consent Mode v2 defaults.</p>
                        </div>

                        @error('ga_measurement_id')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-navy-700/30">

                    {{-- Google Tag Manager ID --}}
                    <div class="space-y-3">
                        <label for="gtm_container_id" class="block text-sm font-bold text-text-secondary">Google Tag Manager Container ID</label>
                        <input type="text" name="gtm_container_id" id="gtm_container_id"
                               value="{{ old('gtm_container_id', $settings['gtm_container_id'] ?? '') }}"
                               placeholder="GTM-XXXXXXX"
                               class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium max-w-sm">

                        <div class="bg-navy-900/50 border border-purple-500/20 rounded-xl p-4 mt-2">
                            <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                How to find your Container ID
                            </h4>
                            <ol class="list-decimal list-inside text-xs text-text-muted space-y-1.5 ml-1">
                                <li>Sign in to <a href="https://tagmanager.google.com" target="_blank" class="text-purple-400 hover:underline">Google Tag Manager</a>.</li>
                                <li>Select your account and container.</li>
                                <li>Your Container ID is shown in the top bar — it starts with <strong>GTM-</strong>.</li>
                            </ol>
                            <p class="text-[10px] text-text-secondary mt-3 italic border-t border-navy-700/30 pt-2">GTM loads after Consent Mode defaults, ensuring the correct signal ordering. Leave blank to disable GTM.</p>
                        </div>

                        @error('gtm_container_id')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION: AI Features & LLMs -->
            <div id="section-llm" class="scroll-mt-[100px] mt-16 section-block">
                <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 mt-8 border-b border-navy-700/50">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 hidden sm:flex shrink-0 items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">AI Features &amp; LLM Config</h2>
                            <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Enable and configure AI-powered capabilities across Atomni.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-sm sm:text-base">
                        Save <span class="hidden sm:inline">Integrations</span>
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-5 sm:p-6">
                    {{-- Fallback banner --}}
                    <div class="mb-6 flex items-start gap-3 bg-amber-500/10 border border-amber-500/30 rounded-xl p-4">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-xs font-bold text-amber-400 mb-1">Recommended: Configure at least 2 AI providers for maximum uptime</p>
                            <p class="text-[11px] text-text-muted leading-relaxed">Atomni uses a <strong class="text-text-secondary">waterfall strategy</strong> — if your primary AI (Gemini) is overloaded or unavailable, it automatically falls back to Anthropic, then OpenAI. Having multiple keys ensures AI features like Trending Topics and Media Alt-Text never go down.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Default Model -->
                        <div class="space-y-3">
                            <label for="default_llm_model" class="block text-sm font-bold text-text-secondary">Default AI Model</label>
                            <div class="relative max-w-sm">
                                <select name="default_llm_model" id="default_llm_model" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium appearance-none">
                                    <option value="auto" {{ (old('default_llm_model', $settings['default_llm_model'] ?? 'auto') == 'auto') ? 'selected' : '' }}>Auto-Select Best Available</option>
                                    <optgroup label="Google Gemini">
                                        <option value="gemini-2.5-flash" {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'gemini-2.5-flash') ? 'selected' : '' }}>Google Gemini 2.5 Flash (Recommended)</option>
                                        <option value="gemini-2.5-pro"   {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'gemini-2.5-pro')   ? 'selected' : '' }}>Google Gemini 2.5 Pro</option>
                                        <option value="gemini-2.0-flash" {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'gemini-2.0-flash') ? 'selected' : '' }}>Google Gemini 2.0 Flash</option>
                                    </optgroup>
                                    <optgroup label="OpenAI">
                                        <option value="gpt-4o"      {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'gpt-4o')      ? 'selected' : '' }}>OpenAI GPT-4o</option>
                                        <option value="gpt-4o-mini" {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'gpt-4o-mini') ? 'selected' : '' }}>OpenAI GPT-4o Mini</option>
                                    </optgroup>
                                    <optgroup label="Anthropic">
                                        <option value="claude-3-5-sonnet" {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'claude-3-5-sonnet') ? 'selected' : '' }}>Anthropic Claude 3.5 Sonnet</option>
                                        <option value="claude-3-5-haiku"  {{ (old('default_llm_model', $settings['default_llm_model'] ?? '') == 'claude-3-5-haiku')  ? 'selected' : '' }}>Anthropic Claude 3.5 Haiku</option>
                                    </optgroup>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-text-muted">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-text-secondary mt-1">Atomni auto-detects which API keys you have and picks the best model. Override here if you prefer a specific one.</p>
                        </div>

                        <hr class="border-navy-700/30">

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            <!-- OpenAI -->
                            <div class="space-y-3">
                                <label for="open_ai_key" class="block text-sm font-bold text-text-secondary">OpenAI API Key</label>
                                <input type="password" name="open_ai_key" id="open_ai_key" autocomplete="off"
                                       value="{{ old('open_ai_key', $settings['open_ai_key'] ?? '') }}"
                                       placeholder="sk-..."
                                       class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium">
                                <p class="text-[10px] text-text-muted">Powers: GPT-4o article generation, DALL-E 3 AI image generation (fallback), and Media alt-text.</p>

                                <div class="bg-navy-900/50 border border-emerald-500/20 rounded-xl p-4 mt-2">
                                    <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        How to get your OpenAI API Key
                                    </h4>
                                    <ol class="list-decimal list-inside text-xs text-text-muted space-y-1.5 ml-1">
                                        <li>Go to <a href="https://platform.openai.com/signup" target="_blank" class="text-emerald-400 hover:underline">platform.openai.com</a> and create a free account.</li>
                                        <li>Once logged in, click your profile → <strong>API Keys</strong> in the left sidebar.</li>
                                        <li>Click <strong>+ Create new secret key</strong>, give it a name, and copy it.</li>
                                        <li>Paste it into the field above and click <strong>Save Integrations</strong>.</li>
                                    </ol>
                                    <p class="text-[10px] text-amber-400/80 mt-3 italic border-t border-navy-700/30 pt-2">⚠️ OpenAI requires a paid credit balance to use the API. New accounts get $5 free credits. Add a card at <a href="https://platform.openai.com/settings/organization/billing" target="_blank" class="underline">Billing Settings</a> to top up if needed.</p>
                                </div>

                                @error('open_ai_key')
                                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Anthropic -->
                            <div class="space-y-3">
                                <label for="anthropic_key" class="block text-sm font-bold text-text-secondary">Anthropic API Key</label>
                                <input type="password" name="anthropic_key" id="anthropic_key" autocomplete="off"
                                       value="{{ old('anthropic_key', $settings['anthropic_key'] ?? '') }}"
                                       placeholder="sk-ant-..."
                                       class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium">
                                <p class="text-[10px] text-text-muted">Powers: Claude 3.5 Sonnet article generation (fallback when Gemini is unavailable).</p>

                                <div class="bg-navy-900/50 border border-purple-500/20 rounded-xl p-4 mt-2">
                                    <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        How to get your Anthropic API Key
                                    </h4>
                                    <ol class="list-decimal list-inside text-xs text-text-muted space-y-1.5 ml-1">
                                        <li>Go to <a href="https://console.anthropic.com/" target="_blank" class="text-purple-400 hover:underline">console.anthropic.com</a> and sign up for a free account.</li>
                                        <li>Verify your phone number — Anthropic requires this for new accounts.</li>
                                        <li>In the dashboard, go to <strong>API Keys</strong> in the left menu.</li>
                                        <li>Click <strong>Create Key</strong>, copy your key (starts with <code class="bg-navy-800 px-1 rounded">sk-ant-</code>).</li>
                                        <li>Paste it into the field above and click <strong>Save Integrations</strong>.</li>
                                    </ol>
                                    <p class="text-[10px] text-emerald-400/80 mt-3 italic border-t border-navy-700/30 pt-2">✅ Anthropic gives new accounts <strong>$5 free credits</strong> — enough for hundreds of AI article generations. No credit card required to start.</p>
                                </div>

                                @error('anthropic_key')
                                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gemini -->
                            <div class="space-y-3">
                                <label for="gemini_key" class="block text-sm font-bold text-text-secondary">Google Gemini API Key</label>
                                <input type="password" name="gemini_key" id="gemini_key" autocomplete="off"
                                       value="{{ old('gemini_key', $settings['gemini_key'] ?? '') }}"
                                       placeholder="AIzaSy..."
                                       class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium">
                                <p class="text-[10px] text-text-muted">Powers: Gemini article generation + Imagen 3 AI image generation (primary). <span class="text-amber-400 font-semibold">Imagen requires billing to be enabled on your Google Cloud project.</span></p>
                                @error('gemini_key')
                                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: AdSense -->
            <div id="section-adsense" class="scroll-mt-[100px] mt-16 section-block">
                <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 mt-8 border-b border-navy-700/50">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 hidden sm:flex shrink-0 items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">Google AdSense</h2>
                            <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Monetize your original content with AdSense Auto-Ads.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-sm sm:text-base">
                        Save <span class="hidden sm:inline">Integrations</span>
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-5 sm:p-6 space-y-6">
                    {{-- AdSense Publisher ID --}}
                    <div class="space-y-3">
                        <label for="adsense_pub_id" class="block text-sm font-bold text-text-secondary">AdSense Publisher ID</label>
                        <input type="text" name="adsense_pub_id" id="adsense_pub_id"
                               value="{{ old('adsense_pub_id', $settings['adsense_pub_id'] ?? '') }}"
                               placeholder="pub-XXXXXXXXXXXXXXXX"
                               class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium max-w-sm">
                        <p class="text-[10px] text-text-muted">Enter your publisher ID to automatically inject the AdSense script into the site's head section.</p>
                        @error('adsense_pub_id')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-navy-700/30">

                    {{-- Ads.txt Content --}}
                    <div class="space-y-3">
                        <label for="adsense_ads_txt" class="block text-sm font-bold text-text-secondary">ads.txt Content</label>
                        <textarea name="adsense_ads_txt" id="adsense_ads_txt" rows="4"
                                  placeholder="google.com, pub-XXXXXXXXXXXXXXXX, DIRECT, f08c47fec0942fa0"
                                  class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono text-xs resize-y">{{ old('adsense_ads_txt', $settings['adsense_ads_txt'] ?? '') }}</textarea>
                        <p class="text-[10px] text-text-muted">Paste the exact contents of your ads.txt snippet provided by AdSense. This will be automatically served at <a href="/ads.txt" target="_blank" class="text-amber-400 hover:underline">/ads.txt</a>.</p>
                        @error('adsense_ads_txt')
                            <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION: GA4 Data API -->
            <div id="section-ga4" class="scroll-mt-[100px] mt-16 section-block">
                <div class="flex items-center justify-between mb-6 sticky top-[72px] z-20 bg-navy-950/80 backdrop-blur-md py-4 mt-8 border-b border-navy-700/50">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 hidden sm:flex shrink-0 items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-text-primary truncate">GA4 Data API <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-400">Reports Dashboard</span></h2>
                            <p class="text-xs text-text-muted font-medium mt-0.5 hidden sm:block truncate">Pull real traffic data from Google Analytics 4 into your Reports dashboard automatically.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary shrink-0 px-4 py-2 text-sm sm:text-base">
                        Save <span class="hidden sm:inline">Integrations</span>
                    </button>
                </div>

                <div class="glass-card rounded-2xl p-5 sm:p-6 space-y-6">
                    {{-- GA4 Property ID --}}
                    <div class="space-y-3">
                        <label for="ga4_property_id" class="block text-sm font-bold text-text-secondary">GA4 Property ID</label>
                        <input type="text" name="ga4_property_id" id="ga4_property_id"
                               value="{{ old('ga4_property_id', $settings['ga4_property_id'] ?? '') }}"
                               placeholder="123456789"
                               class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono font-medium max-w-xs">
                        <p class="text-[10px] text-text-muted">Found in GA4 → Admin → Property Settings → Property ID (numbers only, not "G-...").</p>
                    </div>

                    <hr class="border-navy-700/30">

                    {{-- Service Account JSON --}}
                    <div class="space-y-3">
                        <label for="ga4_service_account_json" class="block text-sm font-bold text-text-secondary">Service Account JSON Key</label>
                        <textarea name="ga4_service_account_json" id="ga4_service_account_json" rows="6"
                                  placeholder='{"type":"service_account","project_id":"...","private_key":"-----BEGIN RSA PRIVATE KEY-----\n..."}'
                                  class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono text-xs resize-y">{{ old('ga4_service_account_json', $settings['ga4_service_account_json'] ?? '') }}</textarea>

                        <div class="bg-navy-900/50 border border-blue-500/20 rounded-xl p-4 mt-2">
                            <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">How to set up GA4 Data API</h4>
                            <ol class="list-decimal list-inside text-xs text-text-muted space-y-1.5 ml-1">
                                <li>Go to <a href="https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com" target="_blank" class="text-blue-400 hover:underline">Google Cloud Console</a> → Enable <strong>Google Analytics Data API</strong>.</li>
                                <li>Go to <strong>IAM & Admin → Service Accounts</strong> → Create a new service account.</li>
                                <li>Click the service account → <strong>Keys</strong> tab → Add Key → <strong>JSON</strong>. Download the file.</li>
                                <li>Open the downloaded JSON file and paste its entire contents into the field above.</li>
                                <li>In GA4 → Admin → Property Access Management → Add the service account email with <strong>Viewer</strong> role.</li>
                            </ol>
                            <p class="text-[10px] text-text-secondary mt-3 italic border-t border-navy-700/30 pt-2">Once saved, traffic data syncs automatically every night at 03:00. Run <code class="bg-navy-800 px-1 rounded">php artisan ga4:sync</code> to sync immediately.</p>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.section-block');
        const navItems = document.querySelectorAll('.nav-item');

        const observer = new IntersectionObserver((entries) => {
            let activeSectionId = null;
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    activeSectionId = entry.target.id;
                }
            });

            if (activeSectionId) {
                navItems.forEach(item => {
                    const iconBox = item.querySelector('.icon-box');
                    if (item.getAttribute('href') === '#' + activeSectionId) {
                        item.classList.add('bg-accent-blue/10', 'text-accent-blue', 'font-bold');
                        item.classList.remove('text-text-muted', 'hover:bg-navy-800', 'hover:text-text-primary', 'font-medium');
                        if (iconBox) {
                            iconBox.classList.add('bg-accent-blue/20');
                            iconBox.classList.remove('bg-navy-800');
                        }
                    } else {
                        item.classList.remove('bg-accent-blue/10', 'text-accent-blue', 'font-bold');
                        item.classList.add('text-text-muted', 'hover:bg-navy-800', 'hover:text-text-primary', 'font-medium');
                        if (iconBox) {
                            iconBox.classList.remove('bg-accent-blue/20');
                            iconBox.classList.add('bg-navy-800');
                        }
                    }
                });
            }
        }, {
            rootMargin: '-100px 0px -60% 0px',
            threshold: 0
        });

        sections.forEach(section => observer.observe(section));
    });
</script>
@endsection
