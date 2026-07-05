@extends('layouts.app')
@section('title', 'Terms of Service — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">Terms of Service</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">Terms of Service</h1>
        <p class="text-text-muted text-sm">Last updated: March 1, 2026</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="space-y-8 text-text-secondary text-sm leading-relaxed">
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">1. Acceptance of Terms</h2>
            <p>By accessing and using Atomni ("the Service"), you agree to be bound by these Terms of Service. If you do not agree with any part of these terms, you must discontinue use of the Service immediately.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">2. User Accounts</h2>
            <p>When creating an account, you must provide accurate and complete information. You are responsible for maintaining the security of your account credentials and for all activities that occur under your account.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">3. Intellectual Property</h2>
            <p>All content published on Atomni — including articles, images, graphics, logos, and software — is the property of Atomni Media Inc. or its content providers and is protected by copyright and intellectual property laws. Unauthorized reproduction, distribution, or modification is prohibited.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">4. User-Generated Content</h2>
            <p>By submitting comments or other content, you grant Atomni a non-exclusive, worldwide, royalty-free license to use, display, and distribute such content. You retain ownership of your original content but agree not to post content that is defamatory, obscene, or violates the rights of others.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">5. Prohibited Conduct</h2>
            <ul class="list-disc list-inside space-y-2 ml-2">
                <li>Using automated tools to scrape or extract content without authorization</li>
                <li>Impersonating other users, journalists, or Atomni staff</li>
                <li>Posting spam, phishing links, or malicious software</li>
                <li>Circumventing paywalls, rate limits, or access controls</li>
                <li>Harassing, threatening, or intimidating other users</li>
            </ul>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">6. Limitation of Liability</h2>
            <p>Atomni provides content for informational purposes only. We are not liable for any damages arising from your reliance on information published on our platform. The Service is provided "as is" without warranties of any kind.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">7. Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Material changes will be communicated via email or a prominent notice on our site. Continued use of the Service after changes constitutes acceptance of the updated terms.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">8. Contact</h2>
            <p>Questions about these terms? Reach us at <span class="text-electric">legal@atomni.com</span> or visit our <a href="/contact" class="text-electric hover:text-electric-light transition-colors">Contact page</a>.</p>
        </div>
    </div>
</section>

@endsection
