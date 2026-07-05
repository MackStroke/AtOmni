@extends('admin.layouts.app')

@section('title', 'Menus')
@section('page-title', 'Menu Manager')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-text-primary">Menu Manager</h1>
            <p class="text-text-muted mt-1 text-sm">Create and manage dynamic navigation menus for your header, mega menu, and footer.</p>
        </div>
    </div>

    {{-- Create New Menu Card --}}
    <div class="glass-card rounded-2xl p-6 border border-navy-700/30">
        <h2 class="text-lg font-bold text-text-primary flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            Create New Menu
        </h2>
        <form action="{{ route('admin.menus.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
            @csrf
            <div class="flex-1 space-y-1">
                <label class="text-[10px] font-bold text-text-muted uppercase tracking-wider ml-1">Display Name</label>
                <input type="text" name="name" required placeholder="e.g., Main Header Menu"
                       class="w-full px-4 py-2.5 bg-navy-950/40 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 focus:border-accent-blue transition-all placeholder-text-muted/30">
            </div>
            <div class="sm:w-56 space-y-1">
                <label class="text-[10px] font-bold text-text-muted uppercase tracking-wider ml-1">Location Slug</label>
                <select name="location" required
                        class="w-full px-4 py-2.5 bg-navy-950/40 border border-navy-700/30 rounded-xl text-sm text-text-primary focus:ring-2 focus:ring-accent-blue/50 cursor-pointer transition-all">
                    <option value="" disabled selected>Choose location…</option>
                    @php
                        $existingLocations = $menus->pluck('location')->toArray();
                        $allLocations = [
                            'header_navbar' => 'Header Navigation',
                            'mega_menu' => 'Mega Menu',
                            'footer_company' => 'Footer — Company',
                            'footer_categories' => 'Footer — Categories',
                            'footer_legal' => 'Footer — Legal',
                            'footer_resources' => 'Footer — Resources',
                        ];
                    @endphp
                    @foreach($allLocations as $slug => $label)
                        @if(!in_array($slug, $existingLocations))
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endif
                    @endforeach
                    <option value="custom">Custom Location…</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2.5 bg-accent-blue hover:bg-accent-blue-hover text-white text-sm font-bold rounded-xl shadow-lg shadow-accent-blue/20 hover:shadow-accent-blue/40 transition-all whitespace-nowrap">
                    Create Menu
                </button>
            </div>
        </form>
        @if($errors->any())
            <div class="mt-3 text-sm text-rose-400">{{ $errors->first() }}</div>
        @endif
    </div>

    {{-- Existing Menus Grid --}}
    @if($menus->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($menus as $menu)
                <div class="glass-card rounded-2xl p-5 border border-navy-700/30 group hover:border-accent-blue/20 transition-all duration-300 flex flex-col">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-text-primary truncate">{{ $menu->name }}</h3>
                            <span class="inline-flex items-center gap-1.5 mt-1.5 px-2.5 py-1 rounded-lg bg-navy-950/50 text-[10px] font-mono font-bold text-text-muted uppercase tracking-wider border border-navy-700/20">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $menu->location }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 ml-3">
                            @if($menu->is_active)
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" title="Active"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500" title="Inactive"></span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-text-muted mb-5 flex-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            {{ $menu->items_count }} {{ str('item')->plural($menu->items_count) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-navy-700/20">
                        <a href="{{ route('admin.menus.edit', $menu) }}"
                           class="flex-1 text-center px-4 py-2 bg-accent-blue/10 hover:bg-accent-blue hover:text-white text-accent-blue text-sm font-bold rounded-xl transition-all">
                            Edit Items
                        </a>
                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Delete this entire menu? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-rose-500/50 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" title="Delete Menu">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card rounded-2xl p-12 text-center border border-navy-700/20">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-navy-950/40 text-text-muted flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-text-primary mb-1">No menus yet</h3>
            <p class="text-sm text-text-muted max-w-md mx-auto">Create your first menu above. You can assign it to the header, mega menu, or footer sections of your site.</p>
        </div>
    @endif

    {{-- Info Card --}}
    <div class="glass-card rounded-2xl p-5 border border-navy-700/20 text-sm text-text-muted space-y-2">
        <p class="font-bold text-text-secondary flex items-center gap-2">
            <svg class="w-4 h-4 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            How Menu Locations Work
        </p>
        <ul class="pl-6 list-disc space-y-1 text-xs">
            <li><strong>header_nav</strong> — Main navigation links shown in the desktop header bar.</li>
            <li><strong>mega_menu</strong> — Category/topic columns shown in the full-width dropdown mega menu.</li>
            <li><strong>footer_company</strong> / <strong>footer_categories</strong> / <strong>footer_legal</strong> — Footer column links, auto-rendered by location slug.</li>
        </ul>
    </div>
</div>
@endsection
