@extends('layouts.app')

@section('title', 'Atomni — ' . $location->name . ' News')
@section('meta-description', 'Latest ' . $location->name . ' news, analysis, and breaking stories from Atomni.')

@section('content')

{{-- Location Hero --}}
<section class="relative overflow-hidden py-16 mb-8">
    <div class="absolute inset-0 bg-gradient-to-br from-electric-dark via-navy-900 to-navy-950 opacity-90"></div>
    <div class="absolute inset-0 theme-radial-glow-15"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="{{ url('/') }}" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">{{ $location->name }}</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-white mb-3">{{ $location->name }} News</h1>
        <p class="text-text-secondary text-lg max-w-2xl">
            {{ $location?->description ?? 'Stay informed with the latest news, expert analysis, and in-depth reporting from ' . $location->name . '.' }}
        </p>
    </div>
</section>

{{-- Articles Grid --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main content (2/3) --}}
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if(is_countable($posts) && count($posts) > 0 || !is_countable($posts) && $posts)
                    @foreach($posts as $post)
                    <a href="{{ url('article/' . $post->slug) }}" class="block">
                        <article class="glass-card rounded-xl overflow-hidden group cursor-pointer h-full">
                            <div class="relative h-44 overflow-hidden">
                                <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-bold uppercase bg-electric/90 text-white">{{ $post->category?->name ?? 'News' }}</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-heading font-semibold text-base text-text-primary leading-snug mb-2 group-hover:text-electric-light transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-text-secondary text-sm leading-relaxed mb-3 line-clamp-2">{{ $post->excerpt }}</p>
                                <div class="flex items-center justify-between text-xs text-text-muted">
                                    <span>{{ $post->author?->name ?? 'Staff Writer' }}</span>
                                    <span>{{ $post?->reading_time ?? 5 }} min · {{ $post->published_at?->diffForHumans() ?? '' }}</span>
                                </div>
                            </div>
                        </article>
                    </a>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <p class="text-text-muted text-lg">No articles in this location yet.</p>
                    </div>
                @endif
            </div>

            {{-- Real Pagination --}}
            @if($posts->hasPages())
            <div class="mt-10">
                {{ $posts->links('partials.pagination') }}
            </div>
            @endif
        </div>

        {{-- Sidebar (1/3) --}}
        <aside class="space-y-6">
            <div class="glass-card rounded-xl p-6">
                <h3 class="font-heading font-bold text-lg text-text-primary mb-2">📬 Stay Informed</h3>
                <p class="text-text-secondary text-sm mb-4">Get news from {{ $location->name }} delivered to your inbox.</p>
                <form action="{{ route('subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" name="email" autocomplete="email" placeholder="your@email.com" required class="w-full px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">Subscribe Free</button>
                </form>
            </div>

            {{-- Donate Widget --}}
            @include('partials.donate-widget')


        </aside>
    </div>
</section>

@endsection
