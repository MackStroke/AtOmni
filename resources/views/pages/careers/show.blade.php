@extends('layouts.app')
@section('title', $job->title . ' — Careers at Atomni')
@section('content')

<section class="border-b border-navy-700/30 bg-navy-900/50 relative overflow-hidden">
    <!-- Decorative background element -->
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-electric/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 relative z-10">
        <a href="{{ route('careers.index') }}" class="inline-flex items-center text-sm font-medium text-text-muted hover:text-electric transition-colors mb-6">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Open Positions
        </a>

        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <h1 class="font-heading font-bold text-3xl sm:text-4xl text-text-primary">{{ $job->title }}</h1>
                
                <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-text-secondary">
                    @if($job->department)
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-navy-800 text-text-primary border border-navy-700/50">
                            {{ $job->department }}
                        </span>
                    @endif
                    @if($job->location)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $job->location }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5 capitalize">
                        <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ str_replace('-', ' ', $job->type) }}
                    </span>
                    @if($job->closing_date)
                        <span class="flex items-center gap-1.5 text-warning">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Closes {{ $job->closing_date->format('M d, Y') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <a href="#apply" class="shrink-0 inline-flex items-center justify-center px-6 py-3 rounded-lg bg-electric hover:bg-electric-light text-white font-semibold transition-all shadow-lg shadow-electric/20 md:w-auto w-full text-center">
                Apply for this job &rarr;
            </a>
        </div>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12 grid md:grid-cols-3 gap-10">
    {{-- Main Column --}}
    <div class="md:col-span-2 space-y-10">
        
        <div>
            <h2 class="font-heading font-semibold text-xl text-text-primary mb-4 border-b border-navy-700/50 pb-2">About the Role</h2>
            <div class="prose prose-invert max-w-none prose-p:text-text-secondary prose-a:text-electric hover:prose-a:text-electric-light prose-strong:text-text-primary prose-ul:text-text-secondary">
                {!! $job->description !!}
            </div>
        </div>

        @if($job->requirements)
            <div>
                <h2 class="font-heading font-semibold text-xl text-text-primary mb-4 border-b border-navy-700/50 pb-2">Requirements</h2>
                <div class="prose prose-invert max-w-none prose-p:text-text-secondary prose-li:text-text-secondary">
                    {!! $job->requirements !!}
                </div>
            </div>
        @endif

        @if($job->benefits)
            <div>
                <h2 class="font-heading font-semibold text-xl text-text-primary mb-4 border-b border-navy-700/50 pb-2">Benefits</h2>
                <div class="prose prose-invert max-w-none prose-p:text-text-secondary prose-li:text-text-secondary">
                    {!! $job->benefits !!}
                </div>
            </div>
        @endif
        
    </div>

    {{-- Sidebar Application Form --}}
    <div class="md:col-span-1 border-t md:border-t-0 md:border-l border-navy-700/50 pt-10 md:pt-0 md:pl-10">
        <div class="sticky top-24" id="apply">
            <h2 class="font-heading font-bold text-2xl text-text-primary mb-6">Apply Now</h2>
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-alert-red/10 border border-alert-red/30 rounded-lg text-alert-red text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('careers.apply', $job->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-medium text-text-secondary mb-1">First Name *</label>
                        <input type="text" id="first_name" name="first_name" autocomplete="given-name" value="{{ old('first_name') }}" required
                               class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30">
                        @error('first_name') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-medium text-text-secondary mb-1">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" autocomplete="family-name" value="{{ old('last_name') }}" required
                               class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30">
                        @error('last_name') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-text-secondary mb-1">Email Address *</label>
                    <input type="email" id="email" name="email" autocomplete="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30">
                    @error('email') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-medium text-text-secondary mb-1">Phone Number</label>
                    <input type="tel" id="phone" name="phone" autocomplete="tel" value="{{ old('phone') }}"
                           class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30">
                    @error('phone') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="portfolio_url" class="block text-xs font-medium text-text-secondary mb-1">LinkedIn / Portfolio URL</label>
                    <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://"
                           class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary placeholder-navy-600 focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30">
                    @error('portfolio_url') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="resume" class="block text-xs font-medium text-text-secondary mb-1">Resume / CV * (PDF, DOCX)</label>
                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required
                           class="w-full text-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-electric/10 file:text-electric hover:file:bg-electric/20 cursor-pointer">
                    @error('resume') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cover_letter" class="block text-xs font-medium text-text-secondary mb-1">Cover Letter (Optional)</label>
                    <textarea id="cover_letter" name="cover_letter" rows="4"
                              class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 resoze-none">{{ old('cover_letter') }}</textarea>
                    @error('cover_letter') <p class="text-[10px] text-alert-red mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-3 rounded-lg bg-electric hover:bg-electric-light text-white font-semibold transition-all shadow-lg shadow-electric/20">
                        Submit Application
                    </button>
                    <p class="text-[10px] text-text-muted text-center mt-3">By submitting, you agree to our Privacy Policy.</p>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
