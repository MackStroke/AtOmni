@extends('layouts.app')
@section('title', 'DMCA Policy — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">DMCA</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">DMCA Policy</h1>
        <p class="text-text-muted text-sm">Last updated: March 1, 2026</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="space-y-8 text-text-secondary text-sm leading-relaxed">
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Overview</h2>
            <p>Atomni respects the intellectual property rights of others and expects our users to do the same. In accordance with the Digital Millennium Copyright Act (DMCA), we will respond promptly to claims of copyright infringement committed using our platform.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Filing a DMCA Notice</h2>
            <p>If you believe that content on Atomni infringes your copyright, please send a written notice to our designated agent with the following information:</p>
            <ul class="list-disc list-inside space-y-2 ml-2 mt-3">
                <li>A physical or electronic signature of the copyright owner or authorized agent</li>
                <li>Identification of the copyrighted work claimed to have been infringed</li>
                <li>Identification of the infringing material and its location on our site</li>
                <li>Your contact information (address, phone number, and email)</li>
                <li>A statement of good faith belief that the use is not authorized</li>
                <li>A statement, under penalty of perjury, that the information is accurate</li>
            </ul>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Counter-Notification</h2>
            <p>If you believe your content was removed in error, you may file a counter-notification containing your contact information, identification of the removed content, a statement under penalty of perjury that you believe the removal was a mistake, and consent to jurisdiction.</p>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Designated Agent</h2>
            <div class="glass-card rounded-xl p-5 mt-3">
                <p><strong class="text-text-primary">DMCA Agent — Atomni Media Inc.</strong></p>
                <p class="mt-1">Email: <span class="text-electric">dmca@atomni.com</span></p>
                <p>Address: 123 Innovation Drive, San Francisco, CA 94105</p>
            </div>
        </div>
        <div>
            <h2 class="font-heading font-bold text-xl text-text-primary mb-3">Repeat Infringers</h2>
            <p>Atomni will terminate accounts of users who are determined to be repeat infringers in appropriate circumstances.</p>
        </div>
    </div>
</section>

@endsection
