@extends('layouts.app')

@section('title', ($query ? $query . ' — ' : ($tag ? '#' . $tag . ' — ' : '')) . 'Search — Atomni')
@section('meta-description', 'Search Atomni for news articles, opinion pieces, and in-depth analysis.')
@section('robots', 'noindex,follow')

@section('content')

{{-- Flash Info Message (from redirected 404s) --}}
@if(session('info'))
<div class="max-w-3xl mx-auto px-4 sm:px-6 pt-6">
    <div class="flex items-center gap-3 p-4 rounded-xl bg-electric/10 border border-electric/20 text-sm text-electric">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('info') }}</span>
    </div>
</div>
@endif

{{-- Search Hero --}}
<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-6">Search Atomni</h1>
        <form action="{{ route('search') }}" method="GET" class="relative js-search-container">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" placeholder="Search articles, topics, authors..." autocomplete="off" value="{{ $query ?? $tag ?? '' }}" class="js-autocomplete-search w-full pl-12 pr-5 py-4 rounded-xl bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted text-base focus:outline-none focus:border-electric focus:ring-2 focus:ring-electric/30 transition-all">
            
            {{-- Autocomplete Dropdown --}}
            <div class="js-search-dropdown absolute top-full left-0 right-0 mt-2 bg-navy-900 light:bg-white border border-navy-700 light:border-slate-200 rounded-xl shadow-xl overflow-hidden z-50 hidden opacity-0 transition-opacity duration-200 text-left">
                <ul class="js-search-results divide-y divide-navy-800 light:divide-slate-100"></ul>
                <div class="js-search-loading hidden p-4 text-center text-sm text-text-muted">
                    <svg class="animate-spin h-5 w-5 mx-auto text-electric" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </form>
        <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
            <span class="text-text-muted text-sm">Trending:</span>
            @foreach($trendingTags as $t)
                <a href="{{ url('search?tag=' . urlencode($t->name)) }}" class="px-3 py-1.5 rounded-full text-xs font-medium {{ $tag === $t->name ? 'bg-electric text-white' : 'text-text-secondary bg-navy-800 hover:bg-electric hover:text-white' }} transition-all">#{{ $t->name }}</a>
            @endforeach
        </div>
    </div>
</section>

{{-- Search Results --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <p class="text-text-secondary text-sm">
            @if($tag)
                Showing results for <span class="text-electric font-semibold">#{{ $tag }}</span>
            @elseif($query)
                Showing results for <span class="text-electric font-semibold">"{{ $query }}"</span>
            @else
                Browse all articles
            @endif
        </p>
        <span class="text-text-muted text-sm">{{ $results->total() }} {{ \Illuminate\Support\Str::plural('result', $results->total()) }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(is_countable($results) && count($results) > 0 || !is_countable($results) && $results)
            @foreach($results as $post)
            <a href="{{ url('article/' . $post->slug) }}" class="block">
                <article class="glass-card rounded-xl overflow-hidden group cursor-pointer h-full">
                    <div class="relative h-44 overflow-hidden">
                        <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @if($post->category)
                        <div class="absolute top-3 left-3">
                            <span class="px-2 py-1 rounded-md text-xs font-bold uppercase bg-electric/90 text-white">{{ $post->category->name }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-heading font-semibold text-base text-text-primary leading-snug mb-2 group-hover:text-electric-light transition-colors line-clamp-2">{{ $post->title }}</h3>
                        <p class="text-text-secondary text-sm leading-relaxed line-clamp-2">{{ $post->excerpt }}</p>
                        <div class="flex items-center justify-between text-xs text-text-muted mt-3">
                            <span>{{ $post->author?->name ?? 'Staff Writer' }}</span>
                            <span>{{ $post?->reading_time ?? 5 }} min · {{ $post->published_at?->diffForHumans() ?? '' }}</span>
                        </div>
                    </div>
                </article>
            </a>
            @endforeach
        @else
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-text-muted text-lg mb-2">No results found</p>
                <p class="text-text-muted text-sm">Try a different search term or browse by category.</p>
            </div>
        @endif
    </div>

    {{-- Real Pagination --}}
    @if($results->hasPages())
    <div class="mt-10">
        {{ $results->appends(request()->query())->links('partials.pagination') }}
    </div>
    @endif
</section>

@endsection
