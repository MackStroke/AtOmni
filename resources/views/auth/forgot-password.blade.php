@extends('layouts.guest')

@section('title', 'Forgot Password - Atomni')

@section('content')
<div class="flex flex-col lg:flex-row min-h-screen">
    
    {{-- Left Side: Branding / Logo --}}
    <div class="hidden lg:flex lg:w-1/2 bg-navy-900 flex-col items-center justify-center relative overflow-hidden border-r border-navy-800">
        {{-- Background pattern element --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        <div class="absolute left-0 right-0 top-0 bottom-0 m-auto h-[400px] w-[400px] rounded-full bg-accent-blue/20 blur-[120px] pointer-events-none"></div>
        
        <div class="relative z-10 text-center flex flex-col items-center px-12">
            @php 
                $logoLight = \App\Models\Setting::get('site_logo');
                $logoDark = \App\Models\Setting::get('site_logo_dark');
                $siteName = \App\Models\Setting::get('site_name', 'Atomni');
            @endphp
            
            @if($logoLight || $logoDark)
                @if($logoLight)
                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-light">
                @endif
                @if($logoDark)
                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoDark) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-dark">
                @endif
            @else
                <img loading="lazy" src="{{ asset('images/atomni-logo-light.svg') }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-light">
                <img loading="lazy" src="{{ asset('images/atomni-logo-dark.svg') }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-dark">
            @endif
            
            <p class="text-lg text-text-muted max-w-md leading-relaxed font-medium">
                Your premier source for breaking news, in-depth analysis, and trending stories.
            </p>
        </div>
    </div>

    {{-- Right Side: Forgot Password Form --}}
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:w-1/2 bg-navy-950 relative">
        <div class="w-full max-w-md p-8 sm:p-10 space-y-8 glass-card border border-navy-800/60 rounded-3xl shadow-2xl relative z-10">
            <div class="text-center">
                
                {{-- Mobile Logo (Hidden on Desktop) --}}
                <div class="lg:hidden flex justify-center mb-6">
                    @if($logoLight || $logoDark)
                        @if($logoLight)
                            <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-12 drop-shadow-xl logo-light">
                        @endif
                        @if($logoDark)
                            <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoDark) }}" alt="{{ $siteName }}" class="h-12 drop-shadow-xl logo-dark">
                        @endif
                    @else
                        <img loading="lazy" src="{{ asset('images/atomni-logo-light.svg') }}" alt="{{ $siteName }}" class="h-12 drop-shadow-xl logo-light">
                        <img loading="lazy" src="{{ asset('images/atomni-logo-dark.svg') }}" alt="{{ $siteName }}" class="h-12 drop-shadow-xl logo-dark">
                    @endif
                </div>

                <h2 class="text-3xl font-extrabold tracking-tight text-text-primary font-heading" style="color: var(--color-text-primary) !important;">
                    Forgot Password?
                </h2>
                <p class="mt-2 text-sm text-text-muted font-medium">
                    No worries, we'll send you reset instructions.
                </p>
            </div>

            <form class="mt-8 space-y-6" action="#" method="GET">
                <div class="space-y-5 rounded-md shadow-sm">
                    <div>
                        <label for="email" class="block text-sm font-bold text-text-secondary mb-1.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>    
                            Email Address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="relative block w-full px-4 py-3.5 text-text-primary placeholder-text-muted bg-navy-900/50 border border-navy-700/50 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all sm:text-sm font-medium"
                            placeholder="Enter your email address">
                    </div>
                </div>

                <div class="px-4 py-3 text-sm font-medium text-electric bg-electric/10 border border-electric/20 rounded-xl flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong>Notice:</strong> The automated password reset feature is currently disabled. Please contact the system administrator to reset your password.
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="alert('Please contact the system administrator to reset your password.')"
                        class="btn-login-premium focus:ring-offset-electric w-full flex items-center justify-center">
                        Reset Password
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
                
                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-text-muted hover:text-text-primary transition-colors flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
