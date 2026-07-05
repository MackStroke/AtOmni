@extends('admin.layouts.app')

@section('title', 'Edit Homepage Section')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="page-header mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.homepage-sections.index') }}" class="text-electric hover:underline text-sm font-medium mb-2 inline-block">&larr; Back to Sections</a>
            <h1 class="text-2xl font-bold text-text-primary">Edit Section: {{ $homepageSection->title }}</h1>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 md:p-8">
        <form action="{{ route('admin.homepage-sections.update', $homepageSection) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            {{-- Title --}}
            <div>
                <label for="title" class="block mb-1.5 text-sm font-medium text-text-secondary">Section Title <span class="text-alert-red">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $homepageSection->title) }}" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                <p class="mt-1 text-xs text-text-muted">The heading displayed above the section.</p>
                @error('title')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
            </div>

            {{-- Layout Type --}}
            <div>
                <label for="layout_type" class="block mb-1.5 text-sm font-medium text-text-secondary">Layout Template <span class="text-alert-red">*</span></label>
                <select name="layout_type" id="layout_type" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                    <option value="">Select a layout...</option>
                    <option value="3d_carousel" {{ old('layout_type', $homepageSection->layout_type) == '3d_carousel' ? 'selected' : '' }}>3D Movie Carousel</option>
                    <option value="tech_complex_grid" {{ old('layout_type', $homepageSection->layout_type) == 'tech_complex_grid' ? 'selected' : '' }}>Technology Complex Grid</option>
                    <option value="horizontal_scroll" {{ old('layout_type', $homepageSection->layout_type) == 'horizontal_scroll' ? 'selected' : '' }}>Horizontal Scroll</option>
                    <option value="standard_grid" {{ old('layout_type', $homepageSection->layout_type) == 'standard_grid' ? 'selected' : '' }}>Standard Grid (3 Columns)</option>
                </select>
                @error('layout_type')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Category Filter --}}
                <div>
                    <label for="category_id" class="block mb-1.5 text-sm font-medium text-text-secondary">Filter by Category</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $homepageSection->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>

                {{-- Tag Filter --}}
                <div>
                    <label for="tag_id" class="block mb-1.5 text-sm font-medium text-text-secondary">Filter by Tag</label>
                    <select name="tag_id" id="tag_id" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                        <option value="">All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ old('tag_id', $homepageSection->tag_id) == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    @error('tag_id')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Post Limit --}}
                <div>
                    <label for="post_limit" class="block mb-1.5 text-sm font-medium text-text-secondary">Post Limit</label>
                    <input type="number" name="post_limit" id="post_limit" value="{{ old('post_limit', $homepageSection->post_limit) }}" min="1" max="20" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                    <p class="mt-1 text-xs text-text-muted">Maximum number of posts to fetch.</p>
                    @error('post_limit')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>

                {{-- Order --}}
                <div>
                    <label for="order" class="block mb-1.5 text-sm font-medium text-text-secondary">Sort Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $homepageSection->order) }}" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                    <p class="mt-1 text-xs text-text-muted">You can also drag-and-drop on the index page.</p>
                    @error('order')<p class="mt-1 text-xs text-alert-red">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 pt-2">
                <div class="relative flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $homepageSection->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-navy-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-electric cursor-pointer toggle-bg"></div>
                </div>
                <label for="is_active" class="text-sm font-medium text-text-primary cursor-pointer">Active (Show on Homepage)</label>
            </div>

            <div class="pt-6 border-t border-navy-700">
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white transition-all bg-electric hover:bg-electric-light rounded-xl shadow-lg shadow-electric/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900">
                    Update Section
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle switch logic
    document.querySelector('.toggle-bg').addEventListener('click', function() {
        const checkbox = document.getElementById('is_active');
        checkbox.checked = !checkbox.checked;
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* TomSelect Dark Theme Adjustments */
    .ts-control {
        background-color: #1a233a !important; /* navy-900 */
        border-color: #2a3449 !important; /* navy-700 */
        color: #f1f5f9 !important;
        border-radius: 0.75rem !important; /* rounded-xl */
        padding: 0.625rem 1rem !important;
    }
    .ts-dropdown, .ts-control, .ts-dropdown.plugin-optgroup_columns .ts-dropdown-content {
        color: #f1f5f9 !important;
    }
    .ts-dropdown {
        background-color: #1a233a !important;
        border-color: #2a3449 !important;
        border-radius: 0.75rem !important;
        overflow: hidden;
    }
    .ts-dropdown .active {
        background-color: #2a3449 !important;
        color: #38bdf8 !important; /* electric */
    }
    .ts-control input {
        color: #f1f5f9 !important;
    }
    .ts-wrapper.multi .ts-control > div {
        background: #0ea5e9;
        color: white;
        border: none;
        border-radius: 4px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#category_id', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
        new TomSelect('#tag_ids', {
            create: false,
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    });
</script>
@endsection
