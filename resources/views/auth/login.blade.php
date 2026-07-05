@extends('layouts.guest')

@section('title', 'Login - Atomni')

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
            
            @if($logoLight && $logoDark)
                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-light">
                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoDark) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-dark">
            @elseif($logoLight)
                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl">
            @elseif($logoDark)
                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($logoDark) }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl">
            @else
                <img loading="lazy" src="{{ asset('images/atomni-logo-light.svg') }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-light">
                <img loading="lazy" src="{{ asset('images/atomni-logo-dark.svg') }}" alt="{{ $siteName }}" class="h-10 mb-8 drop-shadow-2xl logo-dark">
            @endif
            
            <p class="text-lg text-text-muted max-w-md leading-relaxed font-medium">
                {{ \App\Models\Setting::get('website_tagline', 'Your premier source for breaking news, in-depth analysis, and trending stories.') }}
            </p>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
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
                    Welcome back
                </h2>
                <p class="mt-2 text-sm text-text-muted font-medium">
                    Sign in to your account to continue
                </p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="space-y-5 rounded-md shadow-sm">
                    <div>
                        <label for="email" class="block text-sm font-bold text-text-secondary mb-1.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>    
                            Email Address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="relative block w-full px-4 py-3.5 text-text-primary placeholder-text-muted bg-navy-900/50 border border-navy-700/50 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all sm:text-sm font-medium"
                            placeholder="Enter your Email">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-text-secondary mb-1.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>    
                            Password
                        </label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="relative block w-full px-4 py-3.5 text-text-primary placeholder-text-muted bg-navy-900/50 border border-navy-700/50 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all sm:text-sm font-medium"
                            placeholder="••••••••">
                    </div>
                </div>

                @if($errors->any())
                    <div class="px-4 py-3 text-sm font-medium text-rose-500 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 text-accent-blue bg-navy-900 border-navy-700/50 rounded focus:ring-accent-blue focus:ring-offset-navy-950 transition-colors cursor-pointer">
                        <label for="remember" class="block ml-2 text-sm font-medium text-text-muted cursor-pointer select-none">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-bold text-accent-blue hover:text-accent-blue-hover transition-colors">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="btn-login-premium focus:ring-offset-electric">
                        Sign in to Dashboard
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
                
                {{-- <div class="mt-4 pt-6 border-t border-navy-700/30 text-center flex flex-col gap-1 text-xs font-medium text-text-muted">
                    <p class="uppercase tracking-wider mb-2 text-navy-500">Demo Credentials</p>
                    <p>Admin: <span class="text-text-secondary select-all">admin@atomni.com</span> / <span class="text-text-secondary select-all">password</span></p>
                </div> --}}
            </form>
        </div>
    </div>
</div>
@endsection
