@extends('layouts.app')
@section('title', 'Advertise with Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="text-electric text-sm font-semibold uppercase tracking-wider">Advertise</span>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-text-primary mt-3 mb-5">Reach Millions of Engaged Readers</h1>
        <p class="text-text-secondary text-lg max-w-2xl mx-auto">Connect your brand with a highly engaged, educated audience through premium ad placements on Atomni's trusted platform.</p>
    </div>
</section>

{{-- Stats --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @php
            $stats = [
                ['value' => '5M+', 'label' => 'Monthly Readers'],
                ['value' => '8.2 min', 'label' => 'Avg. Time on Site'],
                ['value' => '72%', 'label' => 'Return Visitors'],
                ['value' => '4.8★', 'label' => 'Advertiser Rating'],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="glass-card rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-electric mb-1">{{ $stat['value'] }}</div>
                <div class="text-text-muted text-sm">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</section>

{{-- Ad Placements & Impression Rates --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-navy-700/30">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Ad Locations -->
        <div>
            <h2 class="font-heading font-bold text-3xl text-text-primary mb-6">Available Ad Areas</h2>
            <p class="text-text-secondary text-lg mb-8">Maximize your ROI by reaching our readers right where their attention is highest. All quotes and transactions are processed securely in <strong>INR (₹)</strong>.</p>
            
            <div class="space-y-4">
                <div class="glass-card p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-electric rounded-r-xl"></div>
                    <h3 class="font-heading font-bold text-xl text-text-primary mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-electric" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                        Header Leaderboard
                    </h3>
                    <p class="text-text-muted text-sm mb-4">Prime real estate at the very top of every page. Unmissable brand awareness.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary">Size: <strong>728×90</strong></span>
                        <span class="px-3 py-1 bg-electric/10 text-electric rounded-full font-bold">~250K Impressions/mo</span>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-emerald-500 rounded-r-xl"></div>
                    <h3 class="font-heading font-bold text-xl text-text-primary mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        In-Article Body
                    </h3>
                    <p class="text-text-muted text-sm mb-4">Embedded within the content where reader engagement is at its peak.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary">Size: <strong>Responsive</strong></span>
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full font-bold">~150K Impressions/mo</span>
                    </div>
                </div>

                <div class="glass-card p-6 rounded-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-purple-500 rounded-r-xl"></div>
                    <h3 class="font-heading font-bold text-xl text-text-primary mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Sidebar Sticky
                    </h3>
                    <p class="text-text-muted text-sm mb-4">Remains visible as readers scroll through desktop articles.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary">Size: <strong>300×250 / 300x600</strong></span>
                        <span class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-full font-bold">~100K Impressions/mo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form for Quotes -->
        <div class="glass-card p-8 rounded-2xl border border-navy-700/50 shadow-2xl relative">
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-electric/20 rounded-full blur-2xl"></div>
            <h2 class="font-heading font-bold text-2xl text-text-primary mb-2 relative z-10">Request a Sponsorship Quote</h2>
            <p class="text-text-muted text-sm mb-6 relative z-10">Fill out this form and our partnerships team will get back to you with a custom proposal in INR (₹).</p>
            
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4 relative z-10">
                @csrf
                <input type="hidden" name="subject" value="Advertisement / Sponsorship Request">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-text-secondary mb-1">Company/Name</label>
                        <input type="text" id="name" name="name" autocomplete="organization" required class="w-full bg-navy-900 border border-navy-700 rounded-lg px-4 py-2.5 text-text-primary focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-text-secondary mb-1">Email Address</label>
                        <input type="email" id="email" name="email" autocomplete="email" required class="w-full bg-navy-900 border border-navy-700 rounded-lg px-4 py-2.5 text-text-primary focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                    </div>
                </div>
                
                <div>
                    <label for="ad_type" class="block text-sm font-medium text-text-secondary mb-1">Area of Interest</label>
                    <select id="ad_type" name="ad_type" class="w-full bg-navy-900 border border-navy-700 rounded-lg px-4 py-2.5 text-text-primary focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors appearance-none">
                        <option value="Header Leaderboard">Header Leaderboard</option>
                        <option value="In-Article Body">In-Article Body</option>
                        <option value="Sidebar Sticky">Sidebar Sticky</option>
                        <option value="Sponsored Article">Sponsored Article / Review</option>
                        <option value="Custom Campaign">Custom Campaign</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-text-secondary mb-1">Campaign Goals & Details</label>
                    <textarea id="message" name="message" rows="4" required placeholder="Tell us about your brand and what you hope to achieve..." class="w-full bg-navy-900 border border-navy-700 rounded-lg px-4 py-2 text-text-primary focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors resize-none"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-electric hover:bg-electric-light text-white font-bold py-3 px-4 rounded-lg transition-all duration-300 shadow-lg shadow-electric/25 hover:shadow-electric/40 focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-950">
                    Get My Quote in INR
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
