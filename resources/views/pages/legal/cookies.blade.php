@extends('layouts.app')
@section('title', 'Cookie Policy — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">Cookie Policy</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">Cookie Policy</h1>
        <p class="text-text-muted text-sm">Last updated: March 1, 2026</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="space-y-8 text-text-secondary text-sm leading-relaxed">
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">What Are Cookies?</h2>
            <p>Cookies are small text files placed on your device when you visit a website. They help the site remember your preferences, understand how you use the site, and personalize your experience.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Types of Cookies We Use</h2>
            <div class="space-y-4 mt-4">
                <div class="glass-card rounded-xl p-5">
                    <h3 class="font-heading font-semibold text-text-primary mb-1">Essential Cookies</h3>
                    <p>Required for the site to function properly. These cannot be disabled. They handle authentication, security, and basic functionality.</p>
                </div>
                <div class="glass-card rounded-xl p-5">
                    <h3 class="font-heading font-semibold text-text-primary mb-1">Analytics Cookies</h3>
                    <p>Help us understand how visitors interact with our content by collecting anonymous usage statistics. We use this data to improve our articles and user experience.</p>
                </div>
                <div class="glass-card rounded-xl p-5">
                    <h3 class="font-heading font-semibold text-text-primary mb-1">Preference Cookies</h3>
                    <p>Remember your settings such as language, theme (dark/light mode), and reading preferences to provide a personalized experience on return visits.</p>
                </div>
                <div class="glass-card rounded-xl p-5">
                    <h3 class="font-heading font-semibold text-text-primary mb-1">Advertising Cookies</h3>
                    <p>Used by our advertising partners to deliver relevant ads. These may track your browsing activity across sites. You can opt out via your browser settings.</p>
                </div>
            </div>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Managing Cookies</h2>
            <p>You can control and delete cookies through your browser settings. Note that disabling certain cookies may affect the functionality of our site. Most browsers allow you to block third-party cookies while still enabling essential cookies.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Contact</h2>
            <p>For questions about our cookie practices, email <span class="text-electric">privacy@atomni.com</span>.</p>
        </div>
    </div>
</section>

@endsection
