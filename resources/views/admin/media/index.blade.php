@extends('admin.layouts.app')

@section('title', 'Media Library')
@section('page-title', 'Media Library')

@section('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endsection

@section('content')
<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary">Media Library</h1>
        <p class="text-sm text-text-muted mt-1">Manage files, compress to WebP, add metadata, and crop images.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <!-- Advanced Filters Form -->
        <form method="GET" action="{{ route('admin.media.index') }}" id="filter-form" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="view" value="{{ $viewType }}">
            
            <!-- Search -->
            <div class="relative w-full sm:w-auto">
                <input type="text" aria-label="Search" name="search" value="{{ request('search') }}" placeholder="Search media..." class="w-full pl-9 pr-4 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs focus:border-electric focus:ring-1 focus:ring-electric sm:w-48 h-[38px]">
                <svg class="w-4 h-4 text-text-muted absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Type Filter -->
            <select name="type" onchange="document.getElementById('filter-form').submit()" aria-label="Filter by type" class="px-2.5 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-secondary focus:border-electric focus:ring-1 focus:ring-electric cursor-pointer h-[38px]">
                <option value="">All Types</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Documents</option>
            </select>

            <!-- Date Filter -->
            <select name="date" onchange="document.getElementById('filter-form').submit()" aria-label="Filter by date" class="px-2.5 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-secondary focus:border-electric focus:ring-1 focus:ring-electric cursor-pointer h-[38px]">
                <option value="">All Dates</option>
                @foreach($dates as $date)
                    <option value="{{ $date->value }}" {{ request('date') === $date->value ? 'selected' : '' }}>{{ $date->label }}</option>
                @endforeach
            </select>

            <!-- Sort Filter -->
            <select name="sort" onchange="document.getElementById('filter-form').submit()" aria-label="Sort by" class="px-2.5 py-1.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-xs text-text-secondary focus:border-electric focus:ring-1 focus:ring-electric cursor-pointer h-[38px]">
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="size_desc" {{ request('sort') === 'size_desc' ? 'selected' : '' }}>Size (Largest)</option>
                <option value="size_asc" {{ request('sort') === 'size_asc' ? 'selected' : '' }}>Size (Smallest)</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
            </select>

            @if(request()->anyFilled(['search', 'type', 'date', 'sort']))
                <a href="{{ route('admin.media.index', ['view' => $viewType]) }}" class="px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-semibold hover:bg-rose-500/20 transition-all flex items-center gap-1 h-[38px]" title="Clear all filters">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <!-- View Toggle -->
            <div class="bg-navy-800/50 p-1 rounded-lg border border-navy-700/50 flex shrink-0 h-[38px] items-center">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="p-1 rounded-md {{ $viewType === 'grid' ? 'bg-navy-700 text-electric' : 'text-text-muted hover:text-text-primary' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="p-1 rounded-md {{ $viewType === 'list' ? 'bg-navy-700 text-electric' : 'text-text-muted hover:text-text-primary' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </a>
            </div>
    
            @if($viewType === 'grid')
            <!-- Bulk Select Toggle -->
            <button type="button" onclick="toggleBulkSelectMode()" id="btn-bulk-select-toggle" class="px-3 py-2 border border-navy-700/50 rounded-lg hover:bg-navy-800 text-xs text-text-secondary font-bold transition-all flex items-center gap-1.5 h-[38px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Bulk Select
            </button>
            @endif

            <!-- Upload Button -->
            <button onclick="openUploadModal()" class="btn-primary flex-1 sm:flex-none flex items-center justify-center gap-1.5 h-[38px] text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                UPLOAD
            </button>
        </div>
    </div>
</div>

<div id="alert-container" class="mb-4"></div>

{{-- Media Display Area --}}
<div class="glass-card rounded-2xl overflow-hidden p-4 sm:p-6 mb-8">
    @if($media->count() > 0)
        @if($viewType === 'grid')
            <!-- Grid View -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
                @foreach($media as $file)
                    <div onclick="handleGridCardClick(event, {{ $file->toJson() }})" class="cursor-pointer group relative bg-navy-950/40 border border-navy-700/30 rounded-xl overflow-hidden hover:border-accent-blue/50 transition-colors aspect-square flex flex-col shadow-sm hover:shadow-md media-grid-card" data-id="{{ $file->id }}">
                        
                        <!-- Grid Selection Checkbox overlay -->
                        <div class="bulk-select-checkbox-wrapper absolute top-2.5 right-2.5 hidden z-30" onclick="event.stopPropagation();">
                            <input type="checkbox" aria-label="Select item" value="{{ $file->id }}" onchange="handleGridCheckboxChange()" class="grid-bulk-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric w-4 h-4 cursor-pointer">
                        </div>

                        <div class="flex-1 overflow-hidden bg-navy-900/40 flex items-center justify-center relative p-3">
                            @if(\Illuminate\Support\Str::startsWith($file->mime_type, 'image/'))
                                <img loading="lazy" src="{{ asset('storage/' . $file->optimizedPath()) }}" alt="{{ $file->alt_text ?: $file->file_name }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            @elseif(\Illuminate\Support\Str::startsWith($file->mime_type, 'video/'))
                                <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            @else
                                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        <div class="px-2 sm:px-3 py-1.5 sm:py-2 border-t border-navy-700/30 bg-navy-950/90 absolute bottom-0 w-full transform translate-y-0 md:translate-y-full md:group-hover:translate-y-0 transition-transform z-10">
                            <p class="text-[9px] sm:text-[10px] font-bold text-white truncate">{{ $file->file_name }}</p>
                            <p class="text-[9px] text-text-muted mt-0.5">{{ $file->size_kb }} KB</p>
                        </div>
                        @php
                            $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                            $format = strtoupper($ext) ?: strtoupper(explode('/', $file->mime_type)[1] ?? 'FILE');
                            if ($format === 'SVG+XML') $format = 'SVG';
                            $formatColor = match($format) {
                                'WEBP' => 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30',
                                'JPEG', 'JPG' => 'bg-blue-500/20 text-blue-500 border-blue-500/30',
                                'PNG' => 'bg-purple-500/20 text-purple-500 border-purple-500/30',
                                'GIF' => 'bg-pink-500/20 text-pink-500 border-pink-500/30',
                                'SVG' => 'bg-orange-500/20 text-orange-500 border-orange-500/30',
                                'MP4', 'WEBM' => 'bg-red-500/20 text-red-500 border-red-500/30',
                                'MPEG', 'MP3', 'WAV' => 'bg-indigo-500/20 text-indigo-500 border-indigo-500/30',
                                'PDF' => 'bg-rose-500/20 text-rose-500 border-rose-500/30',
                                default => 'bg-navy-700/50 text-text-muted border-navy-600/50',
                            };
                        @endphp
                        <span class="absolute top-2 left-2 {{ $formatColor }} text-[8px] sm:text-[9px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wider backdrop-blur-sm z-20">{{ $format }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <!-- List View -->
            <!-- Mobile Cards -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($media as $file)
                    <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative cursor-pointer hover:bg-navy-800/30 transition-colors" onclick="openDetailModal({{ $file->toJson() }})">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 shrink-0 rounded-lg bg-navy-900 border border-navy-700/50 flex items-center justify-center overflow-hidden">
                                @if(\Illuminate\Support\Str::startsWith($file->mime_type, 'image/'))
                                    <img loading="lazy" src="{{ asset('storage/' . $file->optimizedPath()) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-text-primary text-sm truncate media-filename-{{ $file->id }}">{{ $file->file_name }}</p>
                                <p class="text-xs text-text-muted truncate mt-0.5 media-alttext-{{ $file->id }}">{{ $file->alt_text ?: 'No Alt Text' }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    @php
                                        $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                        $format = strtoupper($ext) ?: strtoupper(explode('/', $file->mime_type)[1] ?? 'UNKNOWN');
                                        if ($format === 'SVG+XML') $format = 'SVG';
                                        $formatColor = match($format) {
                                            'WEBP' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'JPEG', 'JPG' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            'PNG' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                                            'GIF' => 'bg-pink-500/10 text-pink-500 border-pink-500/20',
                                            'SVG' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                            'MP4', 'WEBM' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            'MPEG', 'MP3', 'WAV' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
                                            'PDF' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                            default => 'bg-navy-800 text-text-muted border-transparent',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 {{ $formatColor }} border rounded text-[9px] uppercase font-bold tracking-wider">{{ $format }}</span>
                                    <span class="text-[10px] text-text-secondary">{{ $file->size_kb }} KB</span>
                                </div>
                            </div>
                            
                            <!-- Mobile Actions -->
                            <div class="flex items-center gap-1 shrink-0 -mr-2" onclick="event.stopPropagation();">
                                @if(\Illuminate\Support\Str::startsWith($file->mime_type, 'image/'))
                                <button type="button" onclick="autoFillMediaRow(event, {{ $file->id }}, this)" class="p-1.5 text-emerald-400 hover:bg-emerald-500/20 rounded transition-colors focus:outline-none" title="Auto-Fill SEO fields with AI">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </button>
                                @endif
                                @if($file->file_path && $file->webp_path && $file->file_path !== $file->webp_path)
                                <form action="{{ route('admin.media.destroy_original', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the uncompressed original file to save space?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-amber-500 hover:bg-amber-500/20 rounded transition-colors focus:outline-none" aria-label="Delete Original">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.media.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this media?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-text-muted hover:text-rose-500 hover:bg-rose-500/10 rounded transition-colors focus:outline-none" aria-label="Delete Media">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-navy-700/30">
                            <div>
                                <span class="text-text-muted block text-[10px] uppercase tracking-wider mb-0.5">Uploader</span>
                                <span class="text-text-secondary font-medium">{{ $file->uploader?->name ?? 'System' }}</span>
                            </div>
                            <div>
                                <span class="text-text-muted block text-[10px] uppercase tracking-wider mb-0.5">Date</span>
                                <span class="text-text-secondary">{{ $file->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <x-admin.bulk-actions resource="media" :actions="['auto_fill' => 'Auto-Fill with AI', 'delete' => 'Delete']" />
                <table class="w-full text-sm text-left mt-3">
                    <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                            <th class="px-4 py-3"><x-admin.sort-header column="file_name" label="File" /></th>
                            <th class="px-4 py-3">Uploader</th>
                            <th class="px-4 py-3"><x-admin.sort-header column="size_kb" label="Size" /></th>
                            <th class="px-4 py-3"><x-admin.sort-header column="mime_type" label="Format" /></th>
                            <th class="px-4 py-3"><x-admin.sort-header column="created_at" label="Date" /></th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-700/20">
                        @foreach($media as $file)
                            <tr class="hover:bg-navy-800/20 cursor-pointer" onclick="openDetailModal({{ $file->toJson() }})">
                                <td class="px-4 py-3 w-10 text-center" onclick="event.stopPropagation();">
                                    <input type="checkbox" aria-label="Select item" value="{{ $file->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric">
                                </td>
                                <td class="px-4 py-3 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-navy-900 border border-navy-700/50 flex items-center justify-center overflow-hidden">
                                        @if(\Illuminate\Support\Str::startsWith($file->mime_type, 'image/'))
                                            <img loading="lazy" src="{{ asset('storage/' . $file->optimizedPath()) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="max-w-[200px]">
                                        <p class="font-semibold text-text-primary truncate media-filename-{{ $file->id }}">{{ $file->file_name }}</p>
                                        <p class="text-xs text-text-muted truncate media-alttext-{{ $file->id }}">{{ $file->alt_text ?: 'No Alt Text' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-text-secondary">{{ $file->uploader?->name ?? 'System' }}</td>
                                <td class="px-4 py-3 text-text-secondary">{{ $file->size_kb }} KB</td>
                                <td class="px-4 py-3">
                                    @php
                                        $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                        $format = strtoupper($ext) ?: strtoupper(explode('/', $file->mime_type)[1] ?? 'UNKNOWN');
                                        if ($format === 'SVG+XML') $format = 'SVG';
                                        $formatColor = match($format) {
                                            'WEBP' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'JPEG', 'JPG' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            'PNG' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                                            'GIF' => 'bg-pink-500/10 text-pink-500 border-pink-500/20',
                                            'SVG' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                            'MP4', 'WEBM' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            'MPEG', 'MP3', 'WAV' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
                                            'PDF' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                            default => 'bg-navy-800 text-text-muted border-transparent',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 {{ $formatColor }} border rounded text-[10px] uppercase font-bold tracking-wider">{{ $format }}</span>
                                </td>
                                <td class="px-4 py-3 text-text-secondary">{{ $file->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 text-right" onclick="event.stopPropagation();">
                                    @if(\Illuminate\Support\Str::startsWith($file->mime_type, 'image/'))
                                    <button type="button" onclick="autoFillMediaRow(event, {{ $file->id }}, this)" class="p-2 text-emerald-400 hover:text-white hover:bg-emerald-500/20 rounded-lg transition-colors focus:outline-none inline-block mr-1" title="Auto-Fill SEO fields with AI">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    </button>
                                    @endif
                                    @if($file->file_path && $file->webp_path && $file->file_path !== $file->webp_path)
                                    <form action="{{ route('admin.media.destroy_original', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the uncompressed original file to save space?')" class="inline-block mr-1">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-amber-500 hover:text-white hover:bg-amber-500 rounded-lg transition-colors focus:outline-none" aria-label="Delete Original" title="Delete Original (Keep WebP)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.media.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this media from the library?')" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-text-muted hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors focus:outline-none" aria-label="Delete Media">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="mt-6 border-t border-navy-700/30 pt-4 flex justify-center">
            {{ $media->appends(['view' => $viewType, 'search' => request('search')])->links() }}
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-20 h-20 bg-navy-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">No Media Found</h3>
        </div>
    @endif
</div>

{{-- Upload / Crop Modal --}}
<div id="upload-modal" class="fixed inset-0 z-50 hidden bg-navy-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-navy-900 border border-navy-700 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-navy-700 flex justify-between items-center bg-navy-950/50">
            <h3 class="font-bold text-lg text-text-primary">Upload & Edit Media</h3>
            <button onclick="closeUploadModal()" class="text-text-muted hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <div class="p-4 md:p-6 flex-1 overflow-y-auto w-full flex flex-col md:flex-row gap-4 md:gap-6">
            
            <!-- Upload Area & Cropper -->
            <div class="flex-1 border-2 border-dashed border-navy-700 hover:border-electric transition-colors rounded-xl relative flex flex-col items-center justify-center min-h-[200px] md:min-h-[300px] bg-navy-950/30 overflow-hidden" id="drop-zone">
                <input type="file" aria-label="Upload File" id="file-input" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" onchange="handleFileSelect(event)">
                
                <div id="upload-placeholder" class="text-center pointer-events-none z-20 flex flex-col items-center justify-center p-6">
                    <svg class="mx-auto h-16 w-16 text-electric mb-4 opacity-80" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <p class="text-lg font-bold text-text-primary mb-2">Drag and drop files here</p>
                    <button type="button" onclick="document.getElementById('file-input').click()" class="btn-primary pointer-events-auto mt-2 mb-4">Select Media</button>
                    <p class="text-xs font-medium text-text-muted max-w-xs text-center">Images will be compressed and converted to WebP automatically.</p>
                </div>

                <div id="crop-container" class="hidden w-full h-full relative z-20">
                    <img loading="lazy" id="crop-image" class="max-w-full block">
                </div>
            </div>

            <!-- Upload Info Sidebar -->
            <div class="w-full md:w-64 flex flex-col gap-4">
                <div id="upload-file-info" class="hidden">
                    <h4 class="font-semibold text-text-primary text-sm mb-1 truncate" id="upload-filename"></h4>
                    <p class="text-xs text-text-muted" id="upload-filesize"></p>
                </div>
                
                <div id="crop-actions" class="hidden flex-col gap-2">
                    <p class="text-xs font-bold text-electric uppercase tracking-wider mb-1">Image Adjust</p>
                    <button type="button" onclick="cropper.zoom(0.1)" class="w-full text-left px-3 py-2 bg-navy-800 hover:bg-navy-700 rounded text-sm text-text-secondary">Zoom In</button>
                    <button type="button" onclick="cropper.zoom(-0.1)" class="w-full text-left px-3 py-2 bg-navy-800 hover:bg-navy-700 rounded text-sm text-text-secondary">Zoom Out</button>
                    <button type="button" onclick="cropper.rotate(90)" class="w-full text-left px-3 py-2 bg-navy-800 hover:bg-navy-700 rounded text-sm text-text-secondary">Rotate 90&deg;</button>
                    
                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1 mt-2">Crop Presets</p>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" onclick="cropper.setAspectRatio(NaN)" class="col-span-1 px-1 py-2 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50" title="Freeform">Free</button>
                        <button type="button" onclick="cropper.setAspectRatio(1)" class="col-span-1 px-1 py-2 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50" title="1:1 Square">1:1</button>
                        <button type="button" onclick="cropper.setAspectRatio(16/9)" class="col-span-1 px-1 py-2 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50" title="16:9 Widescreen">16:9</button>
                        <button type="button" onclick="cropper.setAspectRatio(4/3)" class="col-span-1 px-1 py-2 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50" title="4:3 Standard">4:3</button>
                    </div>
                </div>

                <div class="mt-auto">
                    <!-- Progress Bar -->
                    <div id="upload-progress-container" class="hidden w-full bg-navy-800 rounded-full h-2 mb-4 overflow-hidden">
                        <div id="upload-progress-bar" class="bg-electric h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    
                    <button id="btn-upload" onclick="startUpload()" class="btn-primary w-full opacity-50 cursor-not-allowed" disabled>Start Upload</button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Detail Pane Modal --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden bg-navy-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-navy-900 border border-navy-700 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col md:flex-row shadow-2xl overflow-hidden relative animate-fade-in">
        <button onclick="closeDetailModal()" class="absolute top-4 right-4 z-10 p-2 bg-navy-950/50 rounded-full text-text-muted hover:text-white backdrop-blur"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        
        <!-- Preview Side -->
        <div class="flex-1 bg-navy-950/50 p-4 md:p-6 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-navy-700 min-h-[200px] md:min-h-[300px]">
            <div class="w-full flex-1 flex flex-col items-center justify-center relative overflow-hidden bg-navy-950/20 rounded-xl min-h-[220px] md:min-h-[340px]">
                <img loading="lazy" id="detail-preview" class="max-w-full max-h-[40vh] md:max-h-[50vh] object-contain rounded-lg shadow-lg">
                <div id="detail-icon" class="hidden w-24 h-24 text-slate-500"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div>
            </div>

            <!-- Image Edit Panel (Crop controls) -->
            <div id="image-edit-panel" class="hidden flex-col gap-3 w-full bg-navy-950/30 p-4 rounded-xl border border-navy-800/80 mt-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="detailCropper.zoom(0.1)" class="px-3 py-1.5 bg-navy-800 hover:bg-navy-700 rounded text-xs text-text-secondary font-bold">Zoom In</button>
                        <button type="button" onclick="detailCropper.zoom(-0.1)" class="px-3 py-1.5 bg-navy-800 hover:bg-navy-700 rounded text-xs text-text-secondary font-bold">Zoom Out</button>
                        <button type="button" onclick="detailCropper.rotate(90)" class="px-3 py-1.5 bg-navy-800 hover:bg-navy-700 rounded text-xs text-text-secondary font-bold">Rotate 90&deg;</button>
                    </div>
                    <div class="flex gap-1.5">
                        <button type="button" onclick="detailCropper.setAspectRatio(NaN)" class="px-2.5 py-1.5 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors">Free</button>
                        <button type="button" onclick="detailCropper.setAspectRatio(1)" class="px-2.5 py-1.5 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors">1:1</button>
                        <button type="button" onclick="detailCropper.setAspectRatio(16/9)" class="px-2.5 py-1.5 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors">16:9</button>
                        <button type="button" onclick="detailCropper.setAspectRatio(4/3)" class="px-2.5 py-1.5 bg-navy-800 hover:bg-emerald-500/20 hover:text-emerald-400 rounded text-xs text-text-secondary font-bold transition-colors">4:3</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="saveCroppedDetailImage()" id="btn-save-crop" class="px-4 py-1.5 bg-emerald-500 text-white hover:opacity-90 transition-opacity rounded-lg text-xs font-bold shadow-md shadow-emerald-500/25">Apply Crop</button>
                        <button type="button" onclick="cancelEditImageMode()" class="px-3 py-1.5 bg-navy-800 hover:bg-navy-700 rounded-lg text-xs font-semibold text-text-muted hover:text-text-primary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Side -->
        <div class="w-full md:w-80 p-4 md:p-6 flex flex-col gap-6 overflow-y-auto">
            <div>
                <h3 class="font-bold text-text-primary text-sm mb-4 uppercase tracking-widest">Metadata</h3>
                <form id="media-update-form" onsubmit="updateMedia(event)">
                    <input type="hidden" id="detail-id" name="id">
                    <div class="space-y-4">
                        <div>
                            <label for="detail-filename" class="block text-xs text-text-muted mb-1">File Name</label>
                            <input type="text" aria-label="File Name" id="detail-filename" name="file_name" class="w-full px-3 py-2 bg-navy-800 border border-navy-700 rounded-lg text-sm text-text-primary focus:border-electric focus:ring-1 focus:ring-electric">
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="detail-alt" class="block text-xs text-text-muted mb-1">Alt Text</label>
                                <button type="button" onclick="generateAltText()" id="btn-ai-alt" class="text-[10px] text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    Auto-Generate with AI
                                </button>
                            </div>
                            <input type="text" aria-label="Alt Text" id="detail-alt" name="alt_text" placeholder="Describe the image for SEO..." class="w-full px-3 py-2 bg-navy-800 border border-navy-700 rounded-lg text-sm text-text-primary focus:border-electric focus:ring-1 focus:ring-electric">
                        </div>
                        <button type="submit" id="btn-save-meta" class="px-4 py-2 bg-electric/10 text-electric hover:bg-electric hover:text-white transition-colors rounded-lg text-sm font-bold w-full mt-2">Save Metadata</button>
                    </div>
                </form>
            </div>

            <div class="border-t border-navy-700 pt-5 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-text-muted">Uploaded By:</span> <span id="detail-uploader" class="text-text-primary font-semibold"></span></div>
                <div class="flex justify-between"><span class="text-text-muted">Uploaded On:</span> <span id="detail-date" class="text-text-primary"></span></div>
                <div class="flex justify-between"><span class="text-text-muted">File Size:</span> <span id="detail-size" class="text-text-primary"></span></div>
                <div class="flex justify-between"><span class="text-text-muted">Mime Type:</span> <span id="detail-mime" class="text-text-primary"></span></div>
                <div class="flex justify-between hidden" id="detail-resolution-row"><span class="text-text-muted">Resolution:</span> <span id="detail-resolution" class="text-text-primary"></span></div>
                <div class="flex items-center gap-2 mt-2">
                    <span id="detail-webp-badge" class="hidden"></span>
                </div>
            </div>

            <!-- Usage Tracking Section -->
            <div id="detail-usage-section" class="border-t border-navy-700 pt-5 space-y-2">
                <h4 class="text-xs text-text-muted uppercase tracking-wider font-bold">Used In</h4>
                <div id="detail-usage-list" class="space-y-1.5 text-xs text-text-secondary">
                    <span class="text-text-muted italic">Scanning for usage...</span>
                </div>
            </div>

            <div class="mt-auto pt-6 flex flex-col gap-2">
                <button onclick="copyDetailUrl()" class="px-4 py-2 bg-navy-800 hover:bg-navy-700 transition-colors rounded-lg text-sm text-text-primary font-bold">Copy URL</button>
                <button type="button" onclick="toggleEditImageMode()" id="btn-edit-image-toggle" class="px-4 py-2 bg-navy-800 hover:bg-navy-700 transition-colors rounded-lg text-sm text-text-primary font-bold hidden">Edit Image</button>
                
                <form id="delete-original-form" method="POST" class="hidden" onsubmit="return confirm('Are you sure you want to delete the uncompressed original file to save space? The WebP version will be kept.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition-colors rounded-lg text-sm font-bold">Delete Original (Keep WebP)</button>
                </form>

                <form id="delete-form" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors rounded-lg text-sm font-bold">Delete Entire Media</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Bulk Select Action Bar -->
<div id="grid-bulk-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-navy-900 border border-navy-700/80 rounded-2xl px-6 py-4 shadow-2xl hidden items-center gap-6 animate-slide-up backdrop-blur-md">
    <span class="text-sm font-bold text-text-primary" id="grid-bulk-count">0 items selected</span>
    <div class="h-4 w-px bg-navy-700"></div>
    <div class="flex items-center gap-2">
        <button type="button" onclick="submitGridBulkAction('auto_fill')" class="px-4 py-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm border border-emerald-500/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Auto-Fill AI
        </button>
        <button type="button" onclick="submitGridBulkAction('delete')" class="px-4 py-2 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm border border-rose-500/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete Selected
        </button>
        <button type="button" onclick="exitBulkSelectMode()" class="px-3 py-2 rounded-lg bg-navy-800 hover:bg-navy-700 text-xs font-semibold text-text-muted hover:text-text-primary transition-all">
            Cancel
        </button>
    </div>
</div>

<script>
    let cropper = null;
    let selectedFile = null;

    // Bulk Select globals
    let isBulkSelectModeActive = false;

    // Inline Image Editor globals
    let detailCropper = null;
    let isMediaInUse = false;

    function openUploadModal() {
        document.getElementById('upload-modal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('upload-modal').classList.add('hidden');
        resetUploadModal();
    }

    function resetUploadModal() {
        document.getElementById('file-input').value = '';
        document.getElementById('upload-placeholder').classList.remove('hidden');
        document.getElementById('crop-container').classList.add('hidden');
        document.getElementById('upload-file-info').classList.add('hidden');
        document.getElementById('crop-actions').classList.add('hidden');
        document.getElementById('btn-upload').disabled = true;
        document.getElementById('btn-upload').classList.add('opacity-50', 'cursor-not-allowed');
        document.getElementById('upload-progress-container').classList.add('hidden');
        document.getElementById('upload-progress-bar').style.width = '0%';
        selectedFile = null;
        if(cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    // Grid view card click interceptor
    function handleGridCardClick(event, fileData) {
        if (isBulkSelectModeActive) {
            event.stopPropagation();
            event.preventDefault();
            // Toggle checkbox
            const checkbox = event.currentTarget.querySelector('.grid-bulk-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                handleGridCheckboxChange();
            }
        } else {
            openDetailModal(fileData);
        }
    }

    // Toggle Bulk Select mode
    function toggleBulkSelectMode() {
        isBulkSelectModeActive = !isBulkSelectModeActive;
        const btn = document.getElementById('btn-bulk-select-toggle');
        const checkboxWrappers = document.querySelectorAll('.bulk-select-checkbox-wrapper');

        if (isBulkSelectModeActive) {
            btn.classList.add('bg-electric', 'text-white', 'border-electric');
            btn.classList.remove('hover:bg-navy-800', 'text-text-secondary');
            checkboxWrappers.forEach(w => w.classList.remove('hidden'));
        } else {
            btn.classList.remove('bg-electric', 'text-white', 'border-electric');
            btn.classList.add('hover:bg-navy-800', 'text-text-secondary');
            checkboxWrappers.forEach(w => {
                w.classList.add('hidden');
                const cb = w.querySelector('.grid-bulk-checkbox');
                if (cb) cb.checked = false;
            });
            document.getElementById('grid-bulk-bar').classList.add('hidden');
            document.getElementById('grid-bulk-bar').classList.remove('flex');
            document.querySelectorAll('.media-grid-card').forEach(c => {
                c.classList.remove('border-electric', 'ring-1', 'ring-electric');
            });
        }
    }

    // Update floating bar and card highlights on checkbox changes
    function handleGridCheckboxChange() {
        const checkboxes = document.querySelectorAll('.grid-bulk-checkbox');
        let selectedCount = 0;

        checkboxes.forEach(cb => {
            const card = cb.closest('.media-grid-card');
            if (cb.checked) {
                selectedCount++;
                if (card) card.classList.add('border-electric', 'ring-1', 'ring-electric');
            } else {
                if (card) card.classList.remove('border-electric', 'ring-1', 'ring-electric');
            }
        });

        const bar = document.getElementById('grid-bulk-bar');
        const countLabel = document.getElementById('grid-bulk-count');

        if (selectedCount > 0) {
            countLabel.innerText = selectedCount + (selectedCount === 1 ? ' item' : ' items') + ' selected';
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    // Cancel Bulk Select
    function exitBulkSelectMode() {
        if (isBulkSelectModeActive) {
            toggleBulkSelectMode();
        }
    }

    // Submit bulk action for selected grid items
    function submitGridBulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.grid-bulk-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select at least one media item.');
            return;
        }

        if (action === 'delete') {
            if (!confirm('Are you absolutely sure you want to delete the ' + checkedBoxes.length + ' selected item(s)? This will permanently delete the files from disk!')) {
                return;
            }
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.bulk', 'media') }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        checkedBoxes.forEach(cb => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = cb.value;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // Inline image cropping editor toggles
    let currentDetailMedia = null;

    function toggleEditImageMode() {
        if (!currentDetailMedia) return;

        document.getElementById('media-update-form').classList.add('hidden');
        document.getElementById('btn-edit-image-toggle').classList.add('hidden');
        
        document.getElementById('image-edit-panel').classList.remove('hidden');
        document.getElementById('image-edit-panel').classList.add('flex');

        const img = document.getElementById('detail-preview');
        detailCropper = new Cropper(img, {
            viewMode: 1,
            autoCropArea: 0.8,
            responsive: true,
            background: false
        });
    }

    function cancelEditImageMode() {
        if (detailCropper) {
            detailCropper.destroy();
            detailCropper = null;
        }

        document.getElementById('media-update-form').classList.remove('hidden');
        document.getElementById('btn-edit-image-toggle').classList.remove('hidden');
        
        document.getElementById('image-edit-panel').classList.add('hidden');
        document.getElementById('image-edit-panel').classList.remove('flex');
    }

    function saveCroppedDetailImage() {
        if (!detailCropper || !currentDetailMedia) return;

        const btn = document.getElementById('btn-save-crop');
        const originalText = btn.innerText;
        btn.innerText = 'Cropping...';
        btn.disabled = true;

        const canvas = detailCropper.getCroppedCanvas({
            maxWidth: 4096,
            maxHeight: 4096
        });

        const croppedData = canvas.toDataURL(currentDetailMedia.mime_type, 0.85);

        fetch(updateRouteBase + '/' + currentDetailMedia.id + '/crop', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                image: croppedData
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Image cropped and saved successfully!');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('error', data.error || 'Failed to crop image.');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            showAlert('error', 'Network error during image crop.');
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }

    function handleFileSelect(event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Remove drop zone styling
        const dropZone = document.getElementById('drop-zone');
        if(dropZone) dropZone.classList.remove('border-electric', 'bg-electric/5');

        const files = event.dataTransfer ? event.dataTransfer.files : event.target.files;
        if(!files || files.length === 0) return;

        if (files.length === 1 && files[0].type.startsWith('image/') && !files[0].name.endsWith('.svg') && !files[0].name.endsWith('.gif')) {
            selectedFile = files[0];
            
            // Update Side Info
            document.getElementById('upload-file-info').classList.remove('hidden');
            document.getElementById('upload-filename').innerText = selectedFile.name;
            document.getElementById('upload-filesize').innerText = (selectedFile.size / 1024).toFixed(2) + ' KB';
            
            // Enable Upload button
            document.getElementById('btn-upload').disabled = false;
            document.getElementById('btn-upload').classList.remove('opacity-50', 'cursor-not-allowed');

            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('crop-container').classList.remove('hidden');
            document.getElementById('crop-actions').classList.remove('hidden');
            document.getElementById('crop-actions').classList.add('flex');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('crop-image');
                img.src = e.target.result;
                
                if(cropper) cropper.destroy();
                
                cropper = new Cropper(img, {
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 0.9,
                    responsive: true,
                });
            };
            reader.readAsDataURL(selectedFile);
        } else {
            // Bulk upload or non-image bypasses Cropper
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('crop-container').classList.add('hidden');
            document.getElementById('upload-file-info').classList.add('hidden');
            document.getElementById('crop-actions').classList.add('hidden');
            document.getElementById('crop-actions').classList.remove('flex');
            
            document.getElementById('btn-upload').disabled = true;
            uploadMultipleFiles(files);
        }
    }

    async function uploadMultipleFiles(files) {
        document.getElementById('upload-progress-container').classList.remove('hidden');
        const progressBar = document.getElementById('upload-progress-bar');
        
        let progressText = document.getElementById('upload-progress-text');
        if(!progressText) {
            progressText = document.createElement('p');
            progressText.id = 'upload-progress-text';
            progressText.className = 'text-xs text-text-muted mt-2 text-center font-medium';
            document.getElementById('upload-progress-container').appendChild(progressText);
        }

        let successCount = 0;
        let failCount = 0;

        for (let i = 0; i < files.length; i++) {
            const currentFile = files[i];
            
            progressText.innerText = `Uploading ${i + 1}/${files.length}: ${currentFile.name}...`;
            progressBar.style.width = '0%';

            try {
                await uploadSingleFile(currentFile, progressBar);
                successCount++;
            } catch (err) {
                console.error("Upload failed for", currentFile.name, err);
                failCount++;
            }
        }

        progressText.innerText = `Uploaded ${successCount} files. (${failCount} failed)`;
        progressBar.style.width = '100%';
        progressBar.classList.add('bg-emerald-500');
        progressBar.classList.remove('bg-electric');
        
        setTimeout(() => window.location.reload(), 1500);
    }

    function uploadSingleFile(file, progressBar) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('files[]', file);
            formData.append('_token', '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('admin.media.store') }}', true);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    if(progressBar) progressBar.style.width = Math.round(percentComplete) + '%';
                }
            };

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(xhr.responseText);
                } else {
                    reject(xhr.statusText);
                }
            };

            xhr.onerror = function() {
                reject("Network Error");
            };

            xhr.send(formData);
        });
    }

    function startUpload() {
        if(!selectedFile) return;

        const btn = document.getElementById('btn-upload');
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...`;
        btn.disabled = true;

        document.getElementById('upload-progress-container').classList.remove('hidden');

        if(cropper) {
            cropper.getCroppedCanvas({
                maxWidth: 4096,
                maxHeight: 4096,
                fillColor: '#ffffff'
            }).toBlob((blob) => {
                const formData = new FormData();
                formData.append('files[]', blob, selectedFile.name);
                formData.append('_token', '{{ csrf_token() }}');
                executeUpload(formData, btn);
            }, selectedFile.type, 0.90);
        } else {
            const formData = new FormData();
            formData.append('files[]', selectedFile);
            formData.append('_token', '{{ csrf_token() }}');
            executeUpload(formData, btn);
        }
    }

    function executeUpload(formData, btn) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('admin.media.store') }}', true);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                document.getElementById('upload-progress-bar').style.width = percent + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                showAlert('success', 'File uploaded and converted successfully!');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('error', 'Failed to upload. ' + xhr.responseText);
                btn.innerHTML = 'Start Upload';
                btn.disabled = false;
            }
        };

        xhr.onerror = function() {
            showAlert('error', 'Network error occurred during upload.');
            btn.innerHTML = 'Start Upload';
            btn.disabled = false;
        };

        xhr.send(formData);
    }

    // Detail Modal Functions
    const assetBase = "{{ asset('storage/') }}";
    const updateRouteBase = "{{ url('admin/media') }}";
    let currentDetailUrl = '';

    function openDetailModal(fileData) {
        currentDetailMedia = fileData;
        document.getElementById('detail-modal').classList.remove('hidden');
        
        currentDetailUrl = assetBase + '/' + (fileData.webp_path || fileData.file_path);
        
        const preview = document.getElementById('detail-preview');
        const icon = document.getElementById('detail-icon');
        
        if(fileData.mime_type.startsWith('image/')) {
            preview.src = currentDetailUrl;
            preview.classList.remove('hidden');
            icon.classList.add('hidden');
            document.getElementById('btn-edit-image-toggle').classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
            icon.classList.remove('hidden');
            document.getElementById('btn-edit-image-toggle').classList.add('hidden');
        }

        // Populate Forms
        document.getElementById('detail-id').value = fileData.id;
        document.getElementById('detail-filename').value = fileData.file_name;
        document.getElementById('detail-alt').value = fileData.alt_text || '';
        document.getElementById('detail-uploader').innerText = (fileData.uploader ? fileData.uploader.name : 'System');
        document.getElementById('detail-date').innerText = new Date(fileData.created_at).toLocaleString();
        document.getElementById('detail-size').innerText = fileData.size_kb + ' KB';
        document.getElementById('detail-mime').innerText = fileData.mime_type;
        
        if(fileData.width && fileData.height) {
            document.getElementById('detail-resolution').innerText = fileData.width + ' x ' + fileData.height;
            document.getElementById('detail-resolution-row').classList.remove('hidden');
        } else {
            document.getElementById('detail-resolution-row').classList.add('hidden');
        }

        const webpBadge = document.getElementById('detail-webp-badge');
        let format = (fileData.mime_type.split('/')[1] || 'UNKNOWN').toUpperCase();
        if (format === 'SVG+XML') format = 'SVG';
        
        webpBadge.innerText = format;
        webpBadge.className = 'px-2 py-0.5 border rounded-md text-[10px] uppercase font-bold tracking-widest';
        
        const ext = fileData.file_path.split('.').pop().toUpperCase();
        if (ext) format = ext;
        
        if (format === 'WEBP') {
            webpBadge.classList.add('bg-emerald-500/10', 'text-emerald-500', 'border-emerald-500/20');
        } else if (format === 'JPEG' || format === 'JPG') {
            webpBadge.classList.add('bg-blue-500/10', 'text-blue-500', 'border-blue-500/20');
        } else if (format === 'PNG') {
            webpBadge.classList.add('bg-purple-500/10', 'text-purple-500', 'border-purple-500/20');
        } else if (format === 'GIF') {
            webpBadge.classList.add('bg-pink-500/10', 'text-pink-500', 'border-pink-500/20');
        } else if (format === 'SVG') {
            webpBadge.classList.add('bg-orange-500/10', 'text-orange-500', 'border-orange-500/20');
        } else if (format === 'MP4' || format === 'WEBM') {
            webpBadge.classList.add('bg-red-500/10', 'text-red-500', 'border-red-500/20');
        } else if (format === 'PDF') {
            webpBadge.classList.add('bg-rose-500/10', 'text-rose-500', 'border-rose-500/20');
        } else {
            webpBadge.classList.add('bg-navy-700/50', 'text-text-muted', 'border-navy-600/50');
        }
        webpBadge.classList.remove('hidden');

        document.getElementById('delete-form').action = updateRouteBase + '/' + fileData.id;
        
        const deleteOriginalForm = document.getElementById('delete-original-form');
        if (fileData.file_path && fileData.webp_path && fileData.file_path !== fileData.webp_path) {
            deleteOriginalForm.action = updateRouteBase + '/' + fileData.id + '/original';
            deleteOriginalForm.classList.remove('hidden');
        } else {
            deleteOriginalForm.classList.add('hidden');
        }

        // Fetch usage data
        const usageList = document.getElementById('detail-usage-list');
        usageList.innerHTML = '<span class="text-text-muted italic">Scanning for usage...</span>';
        isMediaInUse = false;

        fetch(updateRouteBase + '/' + fileData.id + '/usage')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.posts.length === 0) {
                        usageList.innerHTML = '<span class="text-emerald-400 font-semibold">✓ Not used in any posts. (Safe to delete)</span>';
                        isMediaInUse = false;
                    } else {
                        isMediaInUse = true;
                        let html = '<span class="text-rose-400 font-semibold block mb-1">⚠ Used in ' + data.posts.length + ' post(s):</span>';
                        data.posts.forEach(post => {
                            html += `<a href="${post.edit_url}" target="_blank" class="block text-electric hover:underline font-medium py-0.5 truncate">↳ ${post.title}</a>`;
                        });
                        usageList.innerHTML = html;
                    }
                } else {
                    usageList.innerHTML = '<span class="text-text-muted">Failed to load usage data.</span>';
                }
            })
            .catch(err => {
                usageList.innerHTML = '<span class="text-text-muted">Network error.</span>';
            });

        // Set form delete safety validation
        document.getElementById('delete-form').onsubmit = function(e) {
            if (isMediaInUse) {
                return confirm('WARNING: This media is currently in use in active posts! Deleting it will result in broken images. Are you absolutely sure you want to permanently delete it?');
            }
            return confirm('Are you sure you want to completely delete this media from the library?');
        };
    }

    function closeDetailModal() {
        cancelEditImageMode();
        document.getElementById('detail-modal').classList.add('hidden');
    }

    function copyDetailUrl() {
        navigator.clipboard.writeText(currentDetailUrl).then(() => {
            alert('URL copied to clipboard!');
        });
    }

    function updateMedia(e) {
        e.preventDefault();
        const id = document.getElementById('detail-id').value;
        const form = document.getElementById('media-update-form');
        const formData = new FormData(form);
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');

        const btn = document.getElementById('btn-save-meta');
        const originalText = btn.innerText;
        btn.innerText = 'Saving...';
        btn.disabled = true;

        fetch(updateRouteBase + '/' + id, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showAlert('success', 'Metadata updated successfully!');
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(err => {
            showAlert('error', 'Failed to update metadata.');
            btn.innerText = originalText;
            btn.disabled = false;
        });
    }

    function generateAltText() {
        const id = document.getElementById('detail-id').value;
        if (!id) return;

        const btn = document.getElementById('btn-ai-alt');
        const originalContent = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...`;
        btn.disabled = true;

        fetch(updateRouteBase + '/' + id + '/alt-text', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('detail-alt').value = data.alt_text;
                if(data.file_name) {
                    document.getElementById('detail-filename').value = data.file_name;
                }
                showAlert('success', 'Alt text and filename generated successfully!');
            } else {
                showAlert('error', data.error || 'Failed to generate metadata.');
            }
        })
        .catch(err => {
            showAlert('error', 'Network error while generating metadata.');
        })
        .finally(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }

    function autoFillMediaRow(event, id, btn) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const originalContent = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        btn.disabled = true;

        fetch(updateRouteBase + '/' + id + '/alt-text', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update elements with class media-filename-{id}
                const filenameElements = document.querySelectorAll('.media-filename-' + id);
                filenameElements.forEach(el => {
                    el.innerText = data.file_name;
                });

                // Update elements with class media-alttext-{id}
                const alttextElements = document.querySelectorAll('.media-alttext-' + id);
                alttextElements.forEach(el => {
                    el.innerText = data.alt_text || 'No Alt Text';
                });

                showAlert('success', 'Metadata auto-filled successfully!');
            } else {
                showAlert('error', data.error || 'Failed to auto-fill metadata.');
            }
        })
        .catch(err => {
            showAlert('error', 'Network error while auto-filling metadata.');
        })
        .finally(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }

    function showAlert(type, msg) {
        const container = document.getElementById('alert-container');
        const color = type === 'success' ? 'emerald' : 'rose';
        container.innerHTML = `
            <div class="px-4 py-3 rounded-xl bg-${color}-500/10 border border-${color}-500/30 text-${color}-500 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ${msg}
                </div>
            </div>
        `;
        setTimeout(() => container.innerHTML = '', 5000);
    }

    // ── Drag & Drop Upload ────────────────────────────────────
    (function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        let dragCounter = 0;

        // Drop zone events
        if(dropZone) {
            dropZone.addEventListener('dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dragCounter++;
                dropZone.classList.add('border-electric', 'bg-electric/5');
                dropZone.classList.remove('border-navy-700');
            });
            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dragCounter--;
                if(dragCounter === 0) {
                    dropZone.classList.remove('border-electric', 'bg-electric/5');
                    dropZone.classList.add('border-navy-700');
                }
            });
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dragCounter = 0;
                dropZone.classList.remove('border-electric', 'bg-electric/5');
                dropZone.classList.add('border-navy-700');
                if(e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect({ target: { files: e.dataTransfer.files }});
                }
            });
        }

        // Page-level drag-drop (opens upload modal automatically)
        let pageDragCounter = 0;
        const pageDropOverlay = document.createElement('div');
        pageDropOverlay.id = 'page-drop-overlay';
        pageDropOverlay.className = 'fixed inset-0 z-[60] bg-electric/10 backdrop-blur-sm border-4 border-dashed border-electric rounded-3xl hidden flex items-center justify-center pointer-events-none';
        pageDropOverlay.innerHTML = '<div class="text-center"><svg class="mx-auto h-16 w-16 text-electric mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg><p class="text-xl font-bold text-electric">Drop files anywhere to upload</p></div>';
        document.body.appendChild(pageDropOverlay);

        document.addEventListener('dragenter', function(e) {
            e.preventDefault();
            pageDragCounter++;
            // Only show overlay if the upload modal is NOT open
            if(document.getElementById('upload-modal').classList.contains('hidden')) {
                pageDropOverlay.classList.remove('hidden');
                pageDropOverlay.classList.add('flex');
            }
        });
        document.addEventListener('dragleave', function(e) {
            e.preventDefault();
            pageDragCounter--;
            if(pageDragCounter === 0) {
                pageDropOverlay.classList.add('hidden');
                pageDropOverlay.classList.remove('flex');
            }
        });
        document.addEventListener('dragover', function(e) { e.preventDefault(); });
        document.addEventListener('drop', function(e) {
            e.preventDefault();
            pageDragCounter = 0;
            pageDropOverlay.classList.add('hidden');
            pageDropOverlay.classList.remove('flex');
            
            if(e.dataTransfer.files.length > 0 && document.getElementById('upload-modal').classList.contains('hidden')) {
                openUploadModal();
                setTimeout(function() {
                    const fi = document.getElementById('file-input');
                    fi.files = e.dataTransfer.files;
                    handleFileSelect({ target: { files: e.dataTransfer.files }});
                }, 100);
            }
        });
    })();
</script>
@endsection
