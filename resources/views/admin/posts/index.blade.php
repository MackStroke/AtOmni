@extends('admin.layouts.app')
@section('title', 'Posts')
@section('content')

<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Posts</h1>
        <p class="text-text-muted text-sm mt-1 truncate">Manage your blog posts and articles.</p>
    </div>
    <div class="flex items-center gap-2">
        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'editor')
            <button type="button" onclick="submitSelectionTo('{{ route('admin.bulk.handle', 'posts') }}', 'analyze_taxonomy', this, 'Auto-Tag All')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-navy-800 hover:bg-navy-700 border border-navy-700/50 text-text-secondary text-sm font-semibold transition-all shadow-sm shrink-0 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="hidden sm:inline">Auto-Tag All</span>
            </button>

            <button type="button" onclick="submitSelectionTo('{{ route('admin.bulk.handle', 'posts') }}', 'analyze_scores', this, 'Score All')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-navy-800 hover:bg-navy-700 border border-navy-700/50 text-text-secondary text-sm font-semibold transition-all shadow-sm shrink-0 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span class="hidden sm:inline">Score All</span>
            </button>
        @endif
        
        <a href="{{ route('admin.posts.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20 shrink-0 whitespace-nowrap">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">New Post</span>
            <span class="sm:hidden">New</span>
        </a>
    </div>
</div>

<div class="mb-5 flex flex-col xl:flex-row xl:items-start gap-3 w-full">
    {{-- Search & Filters --}}
    <form method="GET" action="{{ route('admin.posts.index') }}" id="filter-form" class="flex flex-col gap-3 flex-1 min-w-0">
        {{-- Preserve sorting if it exists --}}
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
        @endif

        {{-- Top Row: Search + Import/Export --}}
        <div class="flex items-center gap-2 w-full">
            {{-- Search Input --}}
            <div class="relative flex-1 min-w-0 group">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted group-focus-within:text-electric transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" aria-label="Search" name="search" value="{{ request('search') }}" placeholder="Search posts…"
                       class="w-full min-w-0 pl-10 pr-4 py-2.5 rounded-xl bg-navy-800/40 border border-navy-700/50 text-sm text-text-primary placeholder-text-muted/60 focus:bg-navy-800/80 focus:border-electric focus:ring-1 focus:ring-electric/50 focus:outline-none transition-all shadow-sm h-[44px]">
            </div>

            {{-- Export / Import --}}
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('admin.posts.export-sample') }}" title="Download Sample CSV" 
                   class="inline-flex items-center justify-center w-[44px] h-[44px] rounded-xl text-text-secondary bg-navy-800/40 hover:text-text-primary hover:bg-navy-700/50 hover:shadow-md border border-navy-700/50 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <a href="{{ route('admin.posts.export') }}" title="Export CSV"
                   class="inline-flex items-center justify-center w-[44px] h-[44px] rounded-xl text-text-secondary bg-navy-800/40 hover:text-text-primary hover:bg-navy-700/50 hover:shadow-md border border-navy-700/50 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
                <label class="cursor-pointer inline-flex items-center justify-center w-[44px] h-[44px] rounded-xl text-text-secondary bg-navy-800/40 hover:text-text-primary hover:bg-navy-700/50 hover:shadow-md border border-navy-700/50 transition-all" title="Import CSV">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <input type="file" name="csv_file" accept=".csv" class="sr-only" onchange="document.getElementById('import-form').submit()" form="import-form">
                </label>
            </div>
        </div>

        {{-- Filters Row: Horizontally scrollable on mobile --}}
        <div class="flex overflow-x-auto scroll-hide items-center gap-2 w-full pb-1">
            {{-- Bulk Actions --}}
            <x-admin.bulk-actions resource="posts" :actions="['delete' => 'Delete', 'draft' => 'Draft', 'publish' => 'Publish', 'auto_taxonomy' => 'Auto-fill Taxonomy']" class="px-2 py-1.5 bg-navy-900/50 border border-navy-700/50 rounded-xl gap-2 h-[44px] shrink-0" :show-banner="false" />
            <div class="flex items-center gap-2 shrink-0 glass-card px-2 py-1.5 rounded-xl border border-navy-700/50">
                <svg class="w-4 h-4 text-text-muted ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                
                <select name="status" aria-label="Filter by status" onchange="document.getElementById('filter-form').submit()"
                        class="w-28 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">All Status</option>
                    <option value="draft"     {{ request('status')==='draft'     ?'selected':'' }}>Draft</option>
                    <option value="published" {{ request('status')==='published' ?'selected':'' }}>Published</option>
                    <option value="scheduled" {{ request('status')==='scheduled' ?'selected':'' }}>Scheduled</option>
                </select>
                
                <div class="w-px h-5 bg-navy-700/50"></div>
                
                <select name="category_id" aria-label="Filter by category" onchange="document.getElementById('filter-form').submit()"
                        class="w-32 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0 glass-card px-2 py-1.5 rounded-xl border border-navy-700/50">
                <svg class="w-4 h-4 text-text-muted ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                
                <select name="author_id" aria-label="Filter by author" onchange="document.getElementById('filter-form').submit()"
                        class="w-32 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">All Authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                    @endforeach
                </select>
                
                <div class="w-px h-5 bg-navy-700/50"></div>
                
                <select name="source" aria-label="Filter by source" onchange="document.getElementById('filter-form').submit()"
                        class="w-28 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">All Sources</option>
                    <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="rss" {{ request('source') === 'rss' ? 'selected' : '' }}>RSS</option>
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0 glass-card px-2 py-1.5 rounded-xl border border-navy-700/50">
                <svg class="w-4 h-4 text-text-muted ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <select name="date_filter" onchange="toggleCustomDateRange(this)" class="w-32 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors" title="Filter by Date">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ request('date_filter') === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ request('date_filter') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('date_filter') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
                <div id="custom-date-inputs" class="items-center gap-1 {{ request('date_filter') === 'custom' ? 'flex' : 'hidden' }}">
                    <input type="date" name="date_from" aria-label="Start date" value="{{ request('date_from') }}" onchange="document.getElementById('filter-form').submit()" class="w-[110px] px-2 py-1.5 rounded-lg bg-navy-900/50 border-none text-[11px] font-medium text-text-secondary focus:ring-1 focus:ring-electric hover:bg-navy-800 transition-colors">
                    <span class="text-[10px] text-navy-400">to</span>
                    <input type="date" name="date_to" aria-label="End date" value="{{ request('date_to') }}" onchange="document.getElementById('filter-form').submit()" class="w-[110px] px-2 py-1.5 rounded-lg bg-navy-900/50 border-none text-[11px] font-medium text-text-secondary focus:ring-1 focus:ring-electric hover:bg-navy-800 transition-colors">
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 glass-card px-2 py-1.5 rounded-xl border border-navy-700/50">
                <span class="text-[10px] font-bold text-text-muted uppercase tracking-wider ml-2">Scores</span>
                <select name="seo_filter" aria-label="Filter by SEO score" onchange="document.getElementById('filter-form').submit()" class="w-24 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">SEO</option>
                    <option value="best" {{ request('seo_filter') === 'best' ? 'selected' : '' }}>Best (90+)</option>
                    <option value="good" {{ request('seo_filter') === 'good' ? 'selected' : '' }}>Good (70-89)</option>
                    <option value="bad" {{ request('seo_filter') === 'bad' ? 'selected' : '' }}>Bad (50-69)</option>
                    <option value="worse" {{ request('seo_filter') === 'worse' ? 'selected' : '' }}>Worse (<50)</option>
                </select>
                <select name="aeo_filter" aria-label="Filter by AEO score" onchange="document.getElementById('filter-form').submit()" class="w-24 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">AEO</option>
                    <option value="best" {{ request('aeo_filter') === 'best' ? 'selected' : '' }}>Best (90+)</option>
                    <option value="good" {{ request('aeo_filter') === 'good' ? 'selected' : '' }}>Good (70-89)</option>
                    <option value="bad" {{ request('aeo_filter') === 'bad' ? 'selected' : '' }}>Bad (50-69)</option>
                    <option value="worse" {{ request('aeo_filter') === 'worse' ? 'selected' : '' }}>Worse (<50)</option>
                </select>
                <select name="geo_filter" aria-label="Filter by GEO score" onchange="document.getElementById('filter-form').submit()" class="w-24 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                    <option value="">GEO</option>
                    <option value="best" {{ request('geo_filter') === 'best' ? 'selected' : '' }}>Best (90+)</option>
                    <option value="good" {{ request('geo_filter') === 'good' ? 'selected' : '' }}>Good (70-89)</option>
                    <option value="bad" {{ request('geo_filter') === 'bad' ? 'selected' : '' }}>Bad (50-69)</option>
                    <option value="worse" {{ request('geo_filter') === 'worse' ? 'selected' : '' }}>Worse (<50)</option>
                </select>
            </div>

            <button type="button" onclick="selectAllMatchingFilters(this)" class="shrink-0 hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-electric bg-electric/10 hover:bg-electric/20 border border-electric/20 transition-colors" title="Select all posts across all pages matching current filters">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Select All
            </button>

            @if(request()->anyFilled(['search', 'status', 'category_id', 'author_id', 'source', 'date_filter', 'date_from', 'date_to', 'seo_filter', 'aeo_filter', 'geo_filter', 'sort']))
                <a href="{{ route('admin.posts.index') }}" class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-alert-red bg-alert-red/10 hover:bg-alert-red/20 border border-alert-red/20 transition-colors" title="Clear all filters and sorting">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear All
                </a>
            @endif
        </div>
    </form>

    <form action="{{ route('admin.posts.import') }}" method="POST" enctype="multipart/form-data" class="hidden" id="import-form">
        @csrf
    </form>
</div>

{{-- Banner for "Select Everything" --}}
<div class="bulk-select-all-banner hidden mb-4 bg-electric/10 border border-electric/30 text-electric px-4 py-2.5 rounded-xl text-sm text-center flex-col sm:flex-row items-center justify-center gap-2 transition-all">
    <span class="bulk-select-page-msg">
        All <strong class="bulk-page-count">0</strong> items on this page are selected.
    </span>
    <button type="button" class="bulk-select-all-btn hidden font-bold hover:underline transition-all">
        Select all <span class="bulk-total-count">0</span> items matching this search
    </button>
    <span class="bulk-select-all-msg hidden">
        All <strong class="bulk-total-count">0</strong> items are selected. 
        <button type="button" class="bulk-clear-btn font-bold text-rose-400 hover:text-rose-500 hover:underline ml-1 transition-all">Clear selection</button>
    </span>
</div>

<div class="space-y-2.5 md:hidden w-full">
    @forelse($posts as $post)
    <div class="glass-card rounded-xl overflow-hidden w-full">

        {{-- ── Top row: checkbox + tiny thumb + all text meta ── --}}
        <div class="flex items-start gap-2.5 p-3 w-full min-w-0">
            {{-- Checkbox --}}
            <div class="shrink-0 flex items-center h-12 pr-1">
                <input type="checkbox" id="mobile-post-checkbox-{{ $post->id }}" name="post_ids[]" aria-label="Select post {{ $post->title }}" value="{{ $post->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric focus:ring-offset-navy-900 cursor-pointer">
            </div>

            {{-- Thumbnail — fixed small size so text gets most space --}}
            <div class="shrink-0 w-12 h-12">
                @if($post->featured_image)
                    <img loading="lazy" src="{{ $post->featuredImageUrl() }}"
                         class="w-12 h-12 rounded-lg object-cover border border-navy-700/40" alt="">
                @else
                    <div class="w-12 h-12 rounded-lg bg-navy-800/60 flex items-center justify-center border border-navy-700/40">
                        <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- All text meta — must stay inside remaining width --}}
            <div class="flex-1 min-w-0 overflow-hidden">
                {{-- Title: hard truncate to 1 line --}}
                <p class="font-semibold text-text-primary text-sm truncate leading-tight">{{ $post->title }}</p>

                {{-- Badges row --}}
                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                    @if($post->kill_switch)
                        <span class="inline-block text-[9px] font-bold px-1.5 py-px rounded-full uppercase bg-alert-red/20 text-alert-red">Killed</span>
                    @else
                        <span class="inline-block text-[9px] font-bold px-1.5 py-px rounded-full uppercase
                            {{ $post->status==='published' ? 'bg-success/15 text-success' : ($post->status==='scheduled' ? 'bg-amber/15 text-amber' : 'bg-navy-600/30 text-text-muted') }}">
                            {{ $post->status }}
                        </span>
                    @endif
                    @if($post->is_featured)
                        <span class="inline-block text-[9px] font-bold text-electric">★</span>
                    @endif
                    @if($post->category)
                        <span class="inline-block text-[9px] text-text-muted truncate max-w-[80px]">{{ $post->category->name }}</span>
                    @endif
                </div>

                {{-- Date + views — single line, truncated --}}
                <p class="text-[9px] text-text-muted mt-0.5 truncate">
                    {{ $post->created_at->format('d M Y') }} &middot; {{ number_format($post->views_count) }} views
                </p>
            </div>
        </div>

        {{-- ── Action grid — 3 equal columns, never overflows ── --}}
        <div class="grid grid-cols-3 border-t border-navy-700/30 divide-x divide-navy-700/30 w-full">

            {{-- Edit --}}
            <a href="{{ route('admin.posts.edit', $post) }}"
               class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-electric hover:bg-electric/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span class="text-[9px] font-medium">Edit</span>
            </a>

            {{-- Takedown / Restore / placeholder --}}
            @if($post->status==='published' && !$post->kill_switch)
                <button type="button" onclick="openKillModal('{{ $post->id }}','{{ addslashes($post->title) }}')"
                        class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-amber hover:bg-amber/10 transition-colors w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <span class="text-[9px] font-medium">Takedown</span>
                </button>
            @elseif($post->kill_switch)
                <form method="POST" action="{{ route('admin.posts.kill', $post) }}" class="contents">
                    @csrf
                    <button class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-success hover:bg-success/10 transition-colors w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="text-[9px] font-medium">Restore</span>
                    </button>
                </form>
            @else
                <div class="flex flex-col items-center justify-center py-2.5 text-text-muted/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                    <span class="text-[9px]">—</span>
                </div>
            @endif

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="contents" onsubmit="return confirm('Delete this post?')">
                @csrf @method('DELETE')
                <button class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-alert-red hover:bg-alert-red/10 transition-colors w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span class="text-[9px] font-medium">Delete</span>
                </button>
            </form>

        </div>
    </div>
    @empty
    <div class="glass-card rounded-2xl p-10 text-center">
        <p class="text-text-muted mb-3">No posts found.</p>
        <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-electric text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create your first post
        </a>
    </div>
    @endforelse
</div>

{{-- ══════════════════════════════════════════
     DESKTOP: TABLE  (hidden on < md)
══════════════════════════════════════════ --}}
<div class="hidden md:block">
    <div class="glass-card rounded-xl overflow-hidden mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
            <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                <tr class="text-left">
                    <th class="px-5 py-3 w-10 text-center"><input type="checkbox" id="master-checkbox" name="master_checkbox" aria-label="Select all posts on page" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric focus:ring-offset-navy-900"></th>
                    <th class="px-5 py-3 w-16">Image</th>
                    <th class="px-5 py-3"><x-admin.sort-header column="title" label="Title" /></th>
                    <th class="px-5 py-3"><x-admin.sort-header column="category_id" label="Category" /></th>
                    <th class="px-5 py-3 hidden lg:table-cell"><x-admin.sort-header column="author_id" label="Author" /></th>
                    <th class="px-5 py-3"><x-admin.sort-header column="status" label="Status" /></th>
                    <th class="px-5 py-3 hidden xl:table-cell"><x-admin.sort-header column="seo_score" label="SEO" /></th>
                    <th class="px-5 py-3 hidden xl:table-cell"><x-admin.sort-header column="aeo_score" label="AEO" /></th>
                    <th class="px-5 py-3 hidden xl:table-cell"><x-admin.sort-header column="geo_score" label="GEO" /></th>
                    <th class="px-5 py-3 hidden lg:table-cell"><x-admin.sort-header column="views_count" label="Views" /></th>
                    <th class="px-5 py-3 hidden lg:table-cell"><x-admin.sort-header column="created_at" label="Date" /></th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-navy-700/20">
                @forelse($posts as $post)
                <tr class="hover:bg-navy-800/20 transition-colors">
                    <td class="px-5 py-3.5 w-10 text-center">
                        <input type="checkbox" id="post-checkbox-{{ $post->id }}" name="post_ids[]" aria-label="Select post {{ $post->title }}" value="{{ $post->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric focus:ring-offset-navy-900">
                    </td>
                    <td class="px-5 py-3.5 w-16">
                        @if($post->featured_image)
                            <img loading="lazy" src="{{ $post->featuredImageUrl() }}" alt="" class="w-12 h-12 rounded object-cover border border-navy-700/50">
                        @else
                            <div class="w-12 h-12 rounded bg-navy-800/50 flex items-center justify-center border border-navy-700/50 grayscale opacity-60">
                                <img loading="lazy" src="{{ asset('images/atomni-logo-light.svg') }}" class="w-8 h-8 object-contain" alt="">
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-text-primary truncate max-w-xs">{{ $post->title }}</p>
                        @if($post->is_featured)<span class="text-[9px] font-bold tracking-wider uppercase text-electric">★ Featured</span>@endif
                    </td>
                    <td class="px-5 py-3.5 text-text-secondary">{{ $post->category?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-text-secondary">{{ $post->author?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        @if($post->kill_switch)
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide bg-alert-red/20 text-alert-red">KILLED</span>
                        @else
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                                {{ $post->status==='published' ? 'bg-success/15 text-success' : ($post->status==='scheduled' ? 'bg-amber/15 text-amber' : 'bg-navy-600/30 text-text-muted') }}">
                                {{ $post->status }}
                            </span>
                        @endif
                    </td>
                    @php
                        $getScoreBadge = function($score) {
                            if (!is_numeric($score)) return '<span class="text-text-muted/50">—</span>';
                            
                            $color = 'bg-alert-red/10 text-alert-red border border-alert-red/20'; // Worse
                            $label = 'Worse';
                            $icon = '<svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';

                            if ($score >= 90) {
                                $color = 'bg-emerald-400/10 text-emerald-400 border border-emerald-400/20';
                                $label = 'Best';
                                $icon = '<svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                            } elseif ($score >= 70) {
                                $color = 'bg-electric/10 text-electric border border-electric/20';
                                $label = 'Good';
                                $icon = '<svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>';
                            } elseif ($score >= 50) {
                                $color = 'bg-amber-400/10 text-amber-400 border border-amber-400/20';
                                $label = 'Bad';
                                $icon = '<svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                            }
                            
                            return '<div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider ' . $color . '" title="' . $label . '">' . $icon . '<span>' . $score . '</span></div>';
                        };
                    @endphp
                    
                    <td class="px-5 py-3.5 hidden xl:table-cell whitespace-nowrap">
                        {!! $getScoreBadge($post->seo_score) !!}
                    </td>
                    <td class="px-5 py-3.5 hidden xl:table-cell whitespace-nowrap">
                        {!! $getScoreBadge($post->aeo_score) !!}
                    </td>
                    <td class="px-5 py-3.5 hidden xl:table-cell whitespace-nowrap">
                        {!! $getScoreBadge($post->geo_score) !!}
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-text-muted">{{ number_format($post->views_count) }}</td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-text-muted">{{ $post->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.posts.edit', $post) }}"
                               class="p-1.5 rounded-lg text-text-muted hover:text-electric hover:bg-electric/10 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($post->status==='published' && !$post->kill_switch)
                                <button type="button" onclick="openKillModal('{{ $post->id }}','{{ addslashes($post->title) }}')"
                                        class="p-1.5 rounded-lg text-text-muted hover:text-amber hover:bg-amber/10 transition-colors" title="Kill Switch">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </button>
                            @elseif($post->kill_switch)
                                <form method="POST" action="{{ route('admin.posts.kill', $post) }}" class="inline">
                                    @csrf
                                    <button class="p-1.5 rounded-lg text-text-muted hover:text-success hover:bg-success/10 transition-colors" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 rounded-lg text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-8 text-center text-text-muted">No posts found. <a href="{{ route('admin.posts.create') }}" class="text-electric hover:underline">Create one →</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Pagination --}}
@if($posts->hasPages())
    <div class="mt-6 flex justify-center">{{ $posts->links() }}</div>
@endif

{{-- ══════════════════════════════════════════
     MOBILE FAB — floating "New Post" button
══════════════════════════════════════════ --}}
<a href="{{ route('admin.posts.create') }}"
   class="md:hidden fixed bottom-6 right-6 z-40 flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-electric hover:bg-electric-light text-white font-semibold text-sm shadow-2xl shadow-electric/40 transition-all active:scale-95">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
    New Post
</a>

{{-- ══════════════════════════════════════════
     KILL SWITCH MODAL
══════════════════════════════════════════ --}}
<div id="kill-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-navy-950/80 backdrop-blur-sm"></div>
    <div class="admin-modal-body">
        <div class="admin-modal-content relative bg-navy-900 border border-navy-700/50 shadow-2xl p-6">
            <form id="kill-form" method="POST" action="">
                @csrf
                <div class="mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-amber/20 mb-4">
                    <svg class="h-6 w-6 text-amber" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-center text-lg font-bold text-text-primary uppercase tracking-wide mb-1">Take Down Post</h3>
                <p class="text-center text-sm text-text-secondary mb-4">Take down <span id="kill-post-title" class="font-semibold text-text-primary"></span>? Optionally redirect to:</p>
                <input type="url" name="redirect_url" placeholder="https://example.com/replacement"
                       class="w-full rounded-xl bg-navy-800/50 border border-navy-700/50 text-text-primary px-4 py-2.5 text-sm focus:border-electric focus:ring-1 focus:ring-electric placeholder-text-muted mb-5">
                <div class="flex flex-col-reverse sm:flex-row gap-2">
                    <button type="button" onclick="closeKillModal()"
                            class="flex-1 py-2.5 rounded-xl bg-navy-800/60 border border-navy-700/50 text-sm font-semibold text-text-primary hover:bg-navy-800 transition-colors">Cancel</button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-amber hover:bg-amber/90 text-sm font-semibold text-navy-900 transition-colors">Confirm Takedown</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function toggleCustomDateRange(select) {
        if (select.value === 'custom') {
            document.getElementById('custom-date-inputs').classList.remove('hidden');
            document.getElementById('custom-date-inputs').classList.add('flex');
        } else {
            document.getElementById('custom-date-inputs').classList.add('hidden');
            document.getElementById('custom-date-inputs').classList.remove('flex');
            document.getElementById('filter-form').submit();
        }
    }

    var killBase = '{{ url("admin/posts") }}';
    function openKillModal(id, title) {
        document.getElementById('kill-post-title').innerText = '"' + title + '"';
        document.getElementById('kill-form').action = killBase + '/' + id + '/kill';
        document.getElementById('kill-modal').classList.remove('hidden');
    }
    function closeKillModal() {
        document.getElementById('kill-modal').classList.add('hidden');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeKillModal(); });

    function selectAllMatchingFilters(btn) {
        if (window.bulkSelectContext && window.bulkSelectContext.isAllSelected) return;
        
        const master = document.querySelector('.bulk-master-checkbox');
        if (master && !master.checked) {
            master.click(); // Select current page, which reveals the bulk select banner
        }
        
        const selectAllBtn = document.querySelector('.bulk-select-all-btn');
        if (selectAllBtn) {
            selectAllBtn.click(); // Trigger the API fetch for all matching IDs
        }
    }

    function submitSelectionTo(actionUrl, actionName, btnElement, originalText) {
        let finalIds = [];
        if (window.bulkSelectContext && window.bulkSelectContext.isAllSelected && window.bulkSelectContext.allIds) {
            finalIds = window.bulkSelectContext.allIds;
        } else {
            const checkedBoxes = document.querySelectorAll('.bulk-item-checkbox:checked');
            checkedBoxes.forEach(cb => finalIds.push(cb.value));
        }

        if (finalIds.length === 0) {
            alert('Please select at least one post.');
            return;
        }

        // Show loading state
        btnElement.innerHTML = '<span class="text-sm">Queuing...</span>';
        btnElement.disabled = true;

        // Create a dynamic form to submit the IDs
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add action parameter
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = actionName;
        form.appendChild(actionInput);

        // Add IDs
        finalIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
