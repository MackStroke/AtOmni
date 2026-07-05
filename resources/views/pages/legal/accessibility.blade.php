@extends('layouts.app')
@section('title', 'Accessibility — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">Accessibility</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">Accessibility Statement</h1>
        <p class="text-text-muted text-sm">Last updated: March 1, 2026</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="space-y-8 text-text-secondary text-sm leading-relaxed">
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Our Commitment</h2>
            <p>Atomni is committed to ensuring digital accessibility for people of all abilities. We strive to meet or exceed the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA standards across our entire platform.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">What We Do</h2>
            <ul class="list-disc list-inside space-y-2 ml-2">
                <li>Provide text alternatives for non-text content (images, icons, multimedia)</li>
                <li>Ensure all functionality is available via keyboard navigation</li>
                <li>Maintain sufficient color contrast ratios for all text and UI elements</li>
                <li>Use semantic HTML and ARIA labels for screen reader compatibility</li>
                <li>Support browser zoom up to 200% without loss of content or functionality</li>
                <li>Provide captions and transcripts for video and audio content</li>
                <li>Conduct regular accessibility audits with assistive technology testing</li>
            </ul>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Known Limitations</h2>
            <p>While we strive for full compliance, some third-party content (embedded videos, advertising) may not fully meet accessibility standards. We are actively working with our partners to improve these elements.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Feedback & Assistance</h2>
            <p>If you encounter any accessibility barriers on Atomni, please let us know. We take all feedback seriously and will work to address issues promptly.</p>
            <div class="glass-card rounded-xl p-5 mt-3">
                <p><strong class="text-text-primary">Accessibility Team</strong></p>
                <p class="mt-1">Email: <span class="text-electric">accessibility@atomni.com</span></p>
                <p>Phone: +1 (555) 123-4567 ext. 200</p>
                <p class="mt-2 text-text-muted text-xs">We aim to respond to accessibility feedback within 2 business days.</p>
            </div>
        </div>
    </div>
</section>

@endsection
