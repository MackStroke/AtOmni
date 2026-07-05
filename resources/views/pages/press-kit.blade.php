@extends('layouts.app')
@section('title', 'Press Kit — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="text-electric text-sm font-semibold uppercase tracking-wider">Press Kit</span>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-text-primary mt-3 mb-5">Brand & Media Resources</h1>
        <p class="text-text-secondary text-lg max-w-2xl mx-auto">Everything you need to feature Atomni in your publication. Download logos, read our brand guidelines, and find media contacts below.</p>
    </div>
</section>

{{-- Brand Assets --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="font-heading font-bold text-2xl text-text-primary mb-8">Brand Assets</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

@php
    $site_logo = \App\Models\Setting::get('site_logo', '');
    $site_logo_dark = \App\Models\Setting::get('site_logo_dark', '');
@endphp

        {{-- Logo Dark --}}
        <div class="glass-card rounded-xl p-8 text-center">
            <div class="bg-navy-950 rounded-lg p-12 mb-4 flex items-center justify-center min-h-[160px]">
                @if($site_logo_dark)
                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($site_logo_dark) }}" alt="Atomni Dark Logo" class="max-h-12 w-auto">
                @else
                    <img loading="lazy" src="{{ asset('images/atomni-logo-dark.svg') }}" alt="Atomni Dark Logo" class="max-h-12 w-auto">
                @endif
            </div>
            <h3 class="font-heading font-semibold text-text-primary text-sm mb-1">Logo — Dark Background</h3>
            <p class="text-text-muted text-xs mb-3">PNG, SVG formats</p>
            <a href="{{ $site_logo_dark ? \Illuminate\Support\Facades\Storage::url($site_logo_dark) : asset('images/atomni-logo-dark.svg') }}" download class="px-4 py-2 rounded-lg bg-navy-800 hover:bg-navy-700 text-text-primary text-xs font-medium transition-colors border border-navy-700 inline-block">Download ↓</a>
        </div>

        {{-- Logo Light --}}
        <div class="glass-card rounded-xl p-8 text-center">
            <div class="bg-white rounded-lg p-12 mb-4 flex items-center justify-center min-h-[160px]">
                @if($site_logo)
                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($site_logo) }}" alt="Atomni Light Logo" class="max-h-12 w-auto">
                @else
                    <img loading="lazy" src="{{ asset('images/atomni-logo-light.svg') }}" alt="Atomni Light Logo" class="max-h-12 w-auto">
                @endif
            </div>
            <h3 class="font-heading font-semibold text-text-primary text-sm mb-1">Logo — Light Background</h3>
            <p class="text-text-muted text-xs mb-3">PNG, SVG formats</p>
            <a href="{{ $site_logo ? \Illuminate\Support\Facades\Storage::url($site_logo) : asset('images/atomni-logo-light.svg') }}" download class="px-4 py-2 rounded-lg bg-navy-800 hover:bg-navy-700 text-text-primary text-xs font-medium transition-colors border border-navy-700 inline-block">Download ↓</a>
        </div>

        {{-- Color Palette --}}
        <div class="glass-card rounded-xl p-8 text-center">
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="h-16 rounded-lg bg-navy-950 border border-navy-700"></div>
                <div class="h-16 rounded-lg bg-electric"></div>
                <div class="h-16 rounded-lg bg-cyan-glow"></div>
                <div class="h-16 rounded-lg bg-navy-900"></div>
                <div class="h-16 rounded-lg bg-amber"></div>
                <div class="h-16 rounded-lg bg-alert-red"></div>
            </div>
            <h3 class="font-heading font-semibold text-text-primary text-sm mb-1">Color Palette</h3>
            <p class="text-text-muted text-xs mb-3">HEX, RGB, HSL values</p>
            <a href="data:text/plain;charset=utf-8,Atomni%20Color%20Palette%0A%0A----------------------%0ADark%20Navy%3A%20%23020617%0AElectric%20Blue%3A%20%2300D2FF%0ACyan%20Glow%3A%20%233A86FF%0ALight%20Navy%3A%20%230F172A%0AAmber%3A%20%23F59E0B%0AAlert%20Red%3A%20%23EF4444" download="atomni-color-palette.txt" class="px-4 py-2 rounded-lg bg-navy-800 hover:bg-navy-700 text-text-primary text-xs font-medium transition-colors border border-navy-700 inline-block">Download ↓</a>
        </div>
    </div>
</section>

{{-- Usage Guidelines --}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-16 border-t border-navy-700/30">
    <h2 class="font-heading font-bold text-2xl text-text-primary mb-6">Usage Guidelines</h2>
    <div class="space-y-4 text-text-secondary text-sm leading-relaxed">
        <div class="glass-card rounded-xl p-6">
            <h3 class="font-heading font-semibold text-text-primary mb-2">✅ Do</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>Use the official logos provided above in press coverage</li>
                <li>Maintain clear space around the logo (minimum 16px on all sides)</li>
                <li>Credit Atomni when quoting our articles or data</li>
            </ul>
        </div>
        <div class="glass-card rounded-xl p-6">
            <h3 class="font-heading font-semibold text-text-primary mb-2">❌ Don't</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>Alter the colors, proportions, or orientation of the logo</li>
                <li>Use the Atomni brand to imply endorsement without permission</li>
                <li>Place the logo on busy backgrounds that reduce legibility</li>
            </ul>
        </div>
    </div>
</section>

{{-- Media Contact --}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-16 border-t border-navy-700/30">
    <div class="glass-card rounded-xl p-8 text-center">
        <h2 class="font-heading font-bold text-2xl text-text-primary mb-3">Media Contact</h2>
        <p class="text-text-secondary mb-4">For press inquiries, interviews, and media partnerships:</p>
        <p class="text-electric font-medium">press@atomni.com</p>
        <p class="text-text-muted text-sm mt-1">Response time: within 4 business hours</p>
    </div>
</section>

@endsection
