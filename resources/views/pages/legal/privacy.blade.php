@extends('layouts.app')
@section('title', 'Privacy Policy — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">Privacy Policy</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">Privacy Policy</h1>
        <p class="text-text-muted text-sm">Last updated: March 1, 2026</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="space-y-8 text-text-secondary text-sm leading-relaxed">
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">1. Information We Collect</h2>
            <p>We collect information you provide directly, such as when you create an account, subscribe to our newsletter, or contact us. This may include your name, email address, and communication preferences.</p>
            <p class="mt-3">We also automatically collect certain information when you visit our site, including your IP address, browser type, operating system, referring URLs, pages viewed, and the dates/times of your visits.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">2. How We Use Your Information</h2>
            <ul class="list-disc list-inside space-y-2 ml-2">
                <li>To provide, maintain, and improve our services</li>
                <li>To send newsletters and editorial content you've subscribed to</li>
                <li>To communicate service updates and respond to inquiries</li>
                <li>To analyze usage patterns and optimize user experience</li>
                <li>To detect and prevent fraud or security threats</li>
            </ul>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">3. Cookies and Tracking</h2>
            <p>We use cookies and similar tracking technologies to enhance your browsing experience. Essential cookies are required for site functionality. Analytics cookies help us understand how visitors interact with our content. You can manage your cookie preferences through your browser settings or our <a href="{{ route('cookies') }}" class="text-electric hover:text-electric-light transition-colors">Cookie Policy</a>.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">4. Data Sharing</h2>
            <p>We do not sell your personal data. We may share information with trusted third-party service providers who assist us in operating our platform (e.g., email delivery services, analytics providers), subject to strict data processing agreements.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">5. Your Rights</h2>
            <p>Depending on your jurisdiction, you may have rights to access, correct, delete, or port your personal data. To exercise these rights, please contact us at <span class="text-electric">privacy@atomni.com</span>.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">6. Data Security</h2>
            <p>We implement industry-standard security measures including encryption in transit (TLS 1.3), encrypted storage, regular security audits, and strict access controls to protect your information.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">7. Contact</h2>
            <p>For privacy-related questions, please contact our Data Protection Officer at <span class="text-electric">privacy@atomni.com</span> or visit our <a href="/contact" class="text-electric hover:text-electric-light transition-colors">Contact page</a>.</p>
        </div>
    </div>
</section>

@endsection
