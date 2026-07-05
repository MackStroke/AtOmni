@extends('layouts.app')
@section('title', 'Careers — Atomni')
@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="text-electric text-sm font-semibold uppercase tracking-wider">Careers</span>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-text-primary mt-3 mb-5">Join Our Newsroom</h1>
        <p class="text-text-secondary text-lg max-w-2xl mx-auto">We're building the future of journalism. Come work with passionate people who believe that great storytelling can change the world.</p>
    </div>
</section>

{{-- Perks --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="font-heading font-bold text-2xl text-text-primary text-center mb-10">Why Atomni?</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @php
            $perks = [
                ['icon' => '🏠', 'title' => 'Remote-First', 'desc' => 'Work from anywhere in the world'],
                ['icon' => '💰', 'title' => 'Competitive Pay', 'desc' => 'Top-quartile salaries + equity'],
                ['icon' => '📚', 'title' => 'Learning Budget', 'desc' => '$2,000/yr for courses & conferences'],
                ['icon' => '🏥', 'title' => 'Full Benefits', 'desc' => 'Health, dental, vision + 401k'],
                ['icon' => '🌴', 'title' => 'Unlimited PTO', 'desc' => 'Take the time you need'],
                ['icon' => '🏋️', 'title' => 'Wellness', 'desc' => 'Gym membership + mental health support'],
                ['icon' => '🤝', 'title' => 'Mentorship', 'desc' => 'Pair with senior journalists'],
                ['icon' => '🚀', 'title' => 'Impact', 'desc' => 'Shape how millions consume news'],
            ];
        @endphp
        @foreach($perks as $perk)
            <div class="glass-card rounded-xl p-5 text-center">
                <div class="text-2xl mb-2">{{ $perk['icon'] }}</div>
                <h3 class="font-heading font-semibold text-sm text-text-primary mb-1">{{ $perk['title'] }}</h3>
                <p class="text-text-muted text-xs">{{ $perk['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Open Positions --}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-16 border-t border-navy-700/30" id="open-roles">
    <div class="text-center mb-10">
        <h2 class="font-heading font-bold text-3xl text-text-primary">Open Positions</h2>
        <p class="text-text-secondary mt-2">Find your next role at Atomni.</p>
    </div>
    
    @if(session('success'))
        <div class="mb-8 p-4 bg-success/10 border border-success/30 rounded-lg text-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @if(is_countable($jobs) && count($jobs) > 0 || !is_countable($jobs) && $jobs)
            @foreach($jobs as $job)
            <div class="glass-card rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group hover:border-electric/40 transition-colors">
                <div>
                    <h3 class="font-heading font-semibold text-text-primary text-lg group-hover:text-electric flex items-center gap-2 transition-colors">
                        {{ $job->title }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-text-secondary">
                        @if($job->department)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-navy-800 border border-navy-700/50">
                                <svg class="w-3.5 h-3.5 text-electric/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $job->department }}
                            </span>
                        @endif
                        @if($job->location)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $job->location }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1 capitalize">
                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ str_replace('-', ' ', $job->type) }}
                        </span>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('careers.show', $job->slug) }}" class="px-5 py-2.5 rounded-lg bg-navy-800 hover:bg-navy-700 text-text-primary text-sm font-medium transition-all shadow-sm border border-navy-700/50">
                        View Details
                    </a>
                    <a href="{{ route('careers.show', $job->slug) }}#apply" class="px-5 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                        Apply Now &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-12 glass-card rounded-xl">
                <svg class="w-12 h-12 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <h3 class="text-lg font-medium text-text-primary mb-2">No Open Positions Currently</h3>
                <p class="text-text-secondary text-sm max-w-md mx-auto">We're not actively hiring for any roles at the moment, but we're always looking for great talent. Check back soon!</p>
            </div>
        @endif
    </div>
</section>

@endsection
