@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')

<div id="alert-container" class="mb-4"></div>

{{-- Page Header --}}
<div class="page-header mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Categories</h1>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" id="auto_hierarchy_btn" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white transition-all bg-gradient-to-r from-electric to-cyan-glow hover:opacity-90 rounded-lg shadow-lg shadow-electric/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            Auto-Organize Hierarchy
        </button>
    </div>
</div>

<style>
    /* Tailwind JIT fallback for new responsive classes */
    .desktop-table-container { display: none; }
    .mobile-cards-container { display: block; }
    .page-layout-container { display: flex; flex-direction: column; gap: 1.5rem; }
    .form-sidebar { width: 100%; flex-shrink: 0; }
    
    @media (min-width: 768px) {
        .desktop-table-container { display: block; }
        .mobile-cards-container { display: none; }
    }
    
    @media (min-width: 1024px) {
        .page-layout-container { flex-direction: row; gap: 2rem; }
        .form-sidebar { width: 320px; }
    }
    
    @media (min-width: 1280px) {
        .form-sidebar { width: 360px; }
    }
</style>

<div class="page-layout-container">
    {{-- LEFT: ADD CATEGORY FORM --}}
    <div class="form-sidebar">
        <div class="glass-card rounded-xl p-5 md:p-6 mb-6 lg:sticky lg:top-24">
            <h2 class="text-lg font-bold text-text-primary mb-5">Add New Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block mb-1.5 text-sm font-medium text-text-secondary">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3 py-2 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50 text-sm">
                    <p class="mt-1 text-[11px] text-text-muted leading-tight">The name is how it appears on your site.</p>
                    @error('name')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="slug" class="block mb-1.5 text-sm font-medium text-text-secondary">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="w-full px-3 py-2 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50 text-sm">
                    <p class="mt-1 text-[11px] text-text-muted leading-tight">The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
                    @error('slug')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="parent_id" class="block mb-1.5 text-sm font-medium text-text-secondary">Parent Category</label>
                    <div class="relative">
                        <select name="parent_id" id="parent_id" class="w-full px-3 py-2 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all text-sm appearance-none cursor-pointer">
                            <option value="">None</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-text-muted">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <p class="mt-1 text-[11px] text-text-muted leading-tight">Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.</p>
                    @error('parent_id')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block mb-1.5 text-sm font-medium text-text-secondary">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50 text-sm">{{ old('description') }}</textarea>
                    <p class="mt-1 text-[11px] text-text-muted leading-tight">The description is not prominent by default; however, some themes may show it.</p>
                    @error('description')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="color_code" class="block mb-1.5 text-sm font-medium text-text-secondary">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color_picker" id="color_picker" class="w-8 h-8 p-0 border-0 bg-transparent rounded cursor-pointer shrink-0" value="{{ old('color_code', '#2D7FF9') }}">
                            <input type="text" name="color_code" id="color_code" value="{{ old('color_code', '#2D7FF9') }}" required class="w-full px-2 py-1.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono uppercase text-xs min-w-0">
                        </div>
                        @error('color_code')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sort_order" class="block mb-1.5 text-sm font-medium text-text-secondary">Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="w-full px-3 py-1.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all text-sm">
                        @error('sort_order')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 w-full md:w-auto text-sm font-semibold text-white transition-all bg-electric hover:bg-electric-light rounded-lg shadow-lg shadow-electric/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900">
                        Add New Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- RIGHT: TABLE / LIST --}}
    <div class="flex-1 min-w-0">
        {{-- Search & Filters & Bulk Actions Row --}}
        <div class="mb-4 flex flex-col gap-3 w-full">
            <form method="GET" action="{{ route('admin.categories.index') }}" id="filter-form" class="flex flex-col gap-3 w-full">
                {{-- Preserve sorting if it exists --}}
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
                @endif

                <div class="flex flex-wrap items-center gap-2 w-full">
                    {{-- Search Input --}}
                    <div class="relative flex-1 min-w-[200px] group">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted group-focus-within:text-electric transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" autocomplete="off" aria-label="Search" name="search" value="{{ request('search') }}" placeholder="Search categories…"
                               class="w-full min-w-0 pl-10 pr-3 py-1.5 rounded-xl bg-navy-800/40 border border-navy-700/50 text-xs text-text-primary placeholder-text-muted/60 focus:bg-navy-800/80 focus:border-electric focus:ring-1 focus:ring-electric/50 focus:outline-none transition-all shadow-sm h-[38px]">
                    </div>

                    {{-- Bulk Actions --}}
                    <x-admin.bulk-actions resource="categories" :actions="['auto_fill' => 'Auto-Fill with AI', 'delete' => 'Delete']" class="px-2 py-1 bg-navy-900/50 border border-navy-700/50 rounded-xl gap-2 h-[38px] shrink-0" :show-banner="false" />

                    {{-- Filters dropdowns --}}
                    <div class="flex items-center gap-2 shrink-0 glass-card px-2 py-1 rounded-xl border border-navy-700/50 h-[38px]">
                        <svg class="w-3.5 h-3.5 text-text-muted ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        
                        <select name="filter" aria-label="Filter by type" onchange="document.getElementById('filter-form').submit()"
                                class="w-28 px-2 py-1 rounded-lg bg-navy-900/50 border-none text-[11px] font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                            <option value="">All Types</option>
                            <option value="top-level" {{ request('filter') === 'top-level' ? 'selected' : '' }}>Top-Level</option>
                            <option value="subcategories" {{ request('filter') === 'subcategories' ? 'selected' : '' }}>Subcategories</option>
                        </select>
                        
                        <div class="w-px h-4 bg-navy-700/50"></div>
                        
                        <select name="count_filter" aria-label="Filter by post count" onchange="document.getElementById('filter-form').submit()"
                                class="w-28 px-2 py-1 rounded-lg bg-navy-900/50 border-none text-[11px] font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
                            <option value="">All Counts</option>
                            <option value="has_posts" {{ request('count_filter') === 'has_posts' ? 'selected' : '' }}>With Posts (> 0)</option>
                            <option value="empty" {{ request('count_filter') === 'empty' ? 'selected' : '' }}>Empty (0)</option>
                        </select>
                    </div>

                    {{-- Clear Filters button --}}
                    @if(request()->anyFilled(['search', 'filter', 'count_filter', 'sort']))
                        <a href="{{ route('admin.categories.index') }}" class="shrink-0 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-alert-red bg-alert-red/10 hover:bg-alert-red/20 border border-alert-red/20 transition-colors h-[38px]" title="Clear all filters and sorting">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Banner for "Select Everything" --}}
        <div class="bulk-select-all-banner hidden mb-4 bg-electric/10 border border-electric/30 text-electric px-4 py-2.5 rounded-xl text-sm text-center flex-col sm:flex-row items-center justify-center gap-2 transition-all">
            <span class="bulk-select-page-msg">
                All <strong class="bulk-page-count">0</strong> categories on this page are selected.
            </span>
            <button type="button" class="bulk-select-all-btn hidden font-bold hover:underline transition-all">
                Select all <span class="bulk-total-count">0</span> categories matching this search
            </button>
            <span class="bulk-select-all-msg hidden">
                All <strong class="bulk-total-count">0</strong> categories are selected. 
                <button type="button" class="bulk-clear-btn font-bold text-rose-400 hover:text-rose-500 hover:underline ml-1 transition-all">Clear selection</button>
            </span>
        </div>
        
        {{-- MOBILE: Card list (hidden md+) --}}
        <div class="mobile-cards-container space-y-2.5 mt-4">
            @if($categories->isEmpty())
                <div class="glass-card rounded-xl p-8 text-center text-text-muted text-sm">No categories yet.</div>
            @else
                @foreach($categories as $category)
                {{-- Parent --}}
                <div class="glass-card rounded-xl overflow-hidden group">
                    <div class="flex items-start gap-3 p-3 min-w-0">
                        <div class="w-8 h-8 rounded-full shrink-0 ring-2 ring-navy-700/30 mt-1 category-color-{{ $category->id }}" style="background-color: {{ $category->color_code }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-electric text-sm truncate">{{ $category->name }}</p>
                            <p class="text-[10px] text-text-muted mt-0.5">{{ $category->slug }} · Order: {{ $category->sort_order }}</p>
                            
                            {{-- Mobile Row Actions --}}
                            <div class="text-[11px] mt-2 flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-electric hover:underline font-medium">Edit</a>
                                <span class="text-navy-500">|</span>
                                <button type="button" onclick="autoFillCategoryRow(event, {{ $category->id }}, this)" class="text-emerald-400 hover:underline font-medium">Auto-Fill</button>
                                <span class="text-navy-500">|</span>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="text-alert-red hover:underline font-medium">Delete</button>
                                </form>
                                <span class="text-navy-500">|</span>
                                <a href="{{ route('category', $category->slug) }}" target="_blank" class="text-navy-300 hover:underline font-medium">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Children --}}
                @foreach($category->children as $child)
                <div class="glass-card rounded-xl overflow-hidden ml-6 group" style="border-left: 2px solid {{ $child->color_code }}40">
                    <div class="flex items-start gap-3 p-3 min-w-0">
                        <div class="w-6 h-6 rounded-full shrink-0 mt-1 category-color-{{ $child->id }}" style="background-color: {{ $child->color_code }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-electric text-sm truncate">↳ {{ $child->name }}</p>
                            <p class="text-[10px] text-text-muted mt-0.5">{{ $child->slug }} · Order: {{ $child->sort_order }}</p>
                            
                            {{-- Mobile Row Actions --}}
                            <div class="text-[11px] mt-2 flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $child) }}" class="text-electric hover:underline font-medium">Edit</a>
                                <span class="text-navy-500">|</span>
                                <button type="button" onclick="autoFillCategoryRow(event, {{ $child->id }}, this)" class="text-emerald-400 hover:underline font-medium">Auto-Fill</button>
                                <span class="text-navy-500">|</span>
                                <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="text-alert-red hover:underline font-medium">Delete</button>
                                </form>
                                <span class="text-navy-500">|</span>
                                <a href="{{ route('category', $child->slug) }}" target="_blank" class="text-navy-300 hover:underline font-medium">View</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endforeach
            @endif
        </div>

        {{-- DESKTOP: Table (hidden below md) --}}
        <div class="desktop-table-container glass-card rounded-2xl overflow-hidden mt-3">
            <div class="table-scroll-container overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                        <tr>
                            <th scope="col" class="px-4 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                            <th scope="col" class="px-4 py-4 font-bold"><x-admin.sort-header column="name" label="Name" /></th>
                            <th scope="col" class="px-4 py-4 font-bold">Description</th>
                            <th scope="col" class="px-4 py-4 font-bold"><x-admin.sort-header column="slug" label="Slug" /></th>
                            <th scope="col" class="px-4 py-4 font-bold text-center"><div class="flex justify-center"><x-admin.sort-header column="posts_count" label="Count" /></div></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-700/10">
                        @if($categories->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 mb-4 flex items-center justify-center rounded-2xl bg-navy-950/40 text-text-muted border border-navy-700/20">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <p class="text-xl font-bold text-text-primary">No categories found</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($categories as $category)
                            <tr class="transition-colors hover:bg-navy-950/30 group">
                                <td class="px-4 py-4 w-10 text-center align-top pt-5">
                                    <input type="checkbox" aria-label="Select item" value="{{ $category->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric">
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="font-bold text-electric text-[15px]">
                                        {{ $category->name }}
                                        @if(request()->has('sort') && $category->parent)
                                            <span class="text-xs text-text-muted ml-2 font-normal">— {{ $category->parent->name }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] mt-1.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-electric hover:underline font-medium">Edit</a>
                                        <span class="text-navy-500">|</span>
                                        <button type="button" onclick="autoFillCategoryRow(event, {{ $category->id }}, this)" class="text-emerald-400 hover:underline font-medium">Auto-Fill</button>
                                        <span class="text-navy-500">|</span>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                            @csrf @method('DELETE')
                                            <button class="text-alert-red hover:underline font-medium">Delete</button>
                                        </form>
                                        <span class="text-navy-500">|</span>
                                        <a href="{{ route('category', $category->slug) }}" target="_blank" class="text-navy-300 hover:underline font-medium">View</a>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-text-muted text-xs align-top pt-5 max-w-[200px] truncate category-description-{{ $category->id }}" title="{{ $category->description }}">
                                    {{ $category->description ?: '—' }}
                                </td>
                                <td class="px-4 py-4 text-text-secondary text-sm align-top pt-5">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-4 py-4 text-center align-top pt-5">
                                    <a href="{{ route('admin.posts.index', ['category_id' => $category->id]) }}" class="text-electric hover:underline font-bold">{{ $category->posts()->count() }}</a>
                                </td>
                            </tr>
                            
                            @if(!request()->has('sort'))
                                @foreach($category->children as $child)
                                <tr class="transition-colors hover:bg-navy-950/40 bg-navy-950/20 group">
                                    <td class="px-4 py-4 w-10 text-center align-top pt-5">
                                        <input type="checkbox" aria-label="Select item" value="{{ $child->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric">
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="font-bold text-electric text-[15px] flex items-center">
                                            <span class="text-navy-500 mr-2">—</span> {{ $child->name }}
                                        </div>
                                        <div class="text-[11px] mt-1.5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-2 ml-4">
                                            <a href="{{ route('admin.categories.edit', $child) }}" class="text-electric hover:underline font-medium">Edit</a>
                                            <span class="text-navy-500">|</span>
                                            <button type="button" onclick="autoFillCategoryRow(event, {{ $child->id }}, this)" class="text-emerald-400 hover:underline font-medium">Auto-Fill</button>
                                            <span class="text-navy-500">|</span>
                                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                                @csrf @method('DELETE')
                                                <button class="text-alert-red hover:underline font-medium">Delete</button>
                                            </form>
                                            <span class="text-navy-500">|</span>
                                            <a href="{{ route('category', $child->slug) }}" target="_blank" class="text-navy-300 hover:underline font-medium">View</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-text-muted text-xs align-top pt-5 max-w-[200px] truncate category-description-{{ $child->id }}" title="{{ $child->description }}">
                                        {{ $child->description ?: '—' }}
                                    </td>
                                    <td class="px-4 py-4 text-text-secondary text-sm align-top pt-5">
                                        {{ $child->slug }}
                                    </td>
                                    <td class="px-4 py-4 text-center align-top pt-5">
                                        <a href="{{ route('admin.posts.index', ['category_id' => $child->id]) }}" class="text-electric hover:underline font-bold">{{ $child->posts()->count() }}</a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="mt-6 flex justify-center">{{ $categories->links() }}</div>
        @endif
    </div>
</div>

<script>
    // Sync color picker with text input
    const colorPicker = document.getElementById('color_picker');
    const colorInput = document.getElementById('color_code');
    
    if (colorPicker && colorInput) {
        colorPicker.addEventListener('input', (e) => {
            colorInput.value = e.target.value.toUpperCase();
        });
        
        colorInput.addEventListener('input', (e) => {
            if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
                colorPicker.value = e.target.value;
            }
        });
    }

    // Auto-organize hierarchy
    const autoHierarchyBtn = document.getElementById('auto_hierarchy_btn');
    if (autoHierarchyBtn) {
        autoHierarchyBtn.addEventListener('click', async function() {
            if (!confirm('This will use AI to completely reorganize your category parent/child relationships based on their names. Are you sure you want to proceed?')) {
                return;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Analyzing...';
            this.disabled = true;

            try {
                const response = await fetch('{{ route("admin.categories.auto-hierarchy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Categories organized successfully! Reloading...');
                    window.location.reload();
                } else {
                    alert(data.message || 'An error occurred.');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            } catch (error) {
                console.error(error);
                alert('A network error occurred. Please try again.');
                this.innerHTML = originalText;
                this.disabled = false;
            }
        });
    }

    async function autoFillCategoryRow(event, id, btn) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const originalContent = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin h-3.5 w-3.5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
        btn.disabled = true;

        try {
            const response = await fetch(`{{ url('admin/categories') }}/${id}/auto-fill`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                // Update description cells
                const descCells = document.querySelectorAll(`.category-description-${id}`);
                descCells.forEach(cell => {
                    cell.innerText = data.description;
                    cell.title = data.description;
                });

                // Update mobile color circles
                const colorCircles = document.querySelectorAll(`.category-color-${id}`);
                colorCircles.forEach(circle => {
                    circle.style.backgroundColor = data.color_code;
                });

                showAlert('success', 'Category metadata auto-filled successfully!');
            } else {
                showAlert('error', data.error || 'Failed to auto-fill category.');
            }
        } catch (error) {
            console.error(error);
            showAlert('error', 'A network error occurred.');
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }

    function showAlert(type, msg) {
        const container = document.getElementById('alert-container');
        if (!container) return;
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
</script>
@endsection
