@extends('admin.layouts.app')
@section('title', 'Tools')
@section('page-title', 'Tools')
@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-5xl">
    <a href="{{ route('admin.tools.site-health') }}" class="glass-card rounded-xl p-6 hover:bg-navy-800/60 transition-colors group">
        <div class="w-12 h-12 rounded-xl bg-green-500/15 flex items-center justify-center mb-4 group-hover:bg-green-500/25 transition-colors">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="font-heading font-semibold text-text-primary mb-1">Site Health</h3>
        <p class="text-sm text-text-secondary">View PHP version, server info, extensions, and storage usage.</p>
    </a>

    <a href="{{ route('admin.tools.import-export') }}" class="glass-card rounded-xl p-6 hover:bg-navy-800/60 transition-colors group">
        <div class="w-12 h-12 rounded-xl bg-blue-500/15 flex items-center justify-center mb-4 group-hover:bg-blue-500/25 transition-colors">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </div>
        <h3 class="font-heading font-semibold text-text-primary mb-1">Import / Export</h3>
        <p class="text-sm text-text-secondary">Export posts as JSON or import content from a file.</p>
    </a>

    <a href="{{ route('admin.tools.cache') }}" class="glass-card rounded-xl p-6 hover:bg-navy-800/60 transition-colors group">
        <div class="w-12 h-12 rounded-xl bg-amber-500/15 flex items-center justify-center mb-4 group-hover:bg-amber-500/25 transition-colors">
            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="font-heading font-semibold text-text-primary mb-1">Cache Manager</h3>
        <p class="text-sm text-text-secondary">Clear view, application, config, and route caches.</p>
    </a>
</div>

@endsection
