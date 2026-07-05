@extends('layouts.app')
@section('title', $page->title . ' — Atomni')
@section('meta-description', Illuminate\Support\Str::limit(strip_tags($page->content), 150))

@section('content')

<section class="py-16 border-b border-navy-700/30">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="/" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary">{{ $page->title }}</span>
        </nav>
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-3">{{ $page->title }}</h1>
        <p class="text-text-muted text-sm">Last updated: {{ $page->updated_at->format('F j, Y') }}</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="prose-atomni space-y-6 text-text-secondary text-sm leading-relaxed">
        {!! $page->content !!}
    </div>
</section>

@endsection
