@extends('admin.layouts.app')

@section('title', 'Edit Page')

@section('content')
<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.pages.index') }}" class="flex items-center justify-center w-10 h-10 transition-colors rounded-xl bg-navy-950/40 text-text-secondary hover:text-accent-blue hover:bg-accent-blue/10 border border-navy-700/20 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title">Edit Page</h1>
    </div>
</div>

<form action="{{ route('admin.pages.update', $page) }}" method="POST" class="max-w-4xl space-y-6">
    @csrf
    @method('PUT')

    <div class="glass-card p-6 border-navy-700/20 rounded-2xl">
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block mb-2 text-sm font-bold text-text-secondary">Page Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required class="w-full px-4 py-3 bg-navy-950/40 border border-navy-700/20 rounded-xl text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted font-medium">
                @error('title')
                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block mb-2 text-sm font-bold text-text-secondary">URL Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" required class="w-full px-4 py-3 bg-navy-950/40 border border-navy-700/20 rounded-xl text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted font-mono text-sm">
                @error('slug')
                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block mb-2 text-sm font-bold text-text-secondary">Content (HTML allowed)</label>
                <textarea name="content" id="content" rows="15" class="w-full px-4 py-3 bg-navy-950/40 border border-navy-700/20 rounded-xl text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted font-mono text-sm">{{ old('content', $page->content) }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Publish Toggle -->
            <div class="flex items-center mt-6 p-4 rounded-xl bg-navy-950/30 border border-navy-700/20">
                <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }} class="w-5 h-5 border-navy-700/30 rounded text-accent-blue focus:ring-accent-blue bg-navy-900/60">
                <label for="is_published" class="ml-3 text-sm font-bold text-text-primary">Publish this page</label>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 mt-4">
        <a href="{{ route('admin.pages.index') }}" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-text-muted hover:text-text-primary transition-colors text-center">Cancel</a>
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-sm font-bold tracking-widest uppercase text-white transition-all duration-300 rounded-xl bg-accent-blue hover:bg-accent-blue-hover hover:scale-[1.02] active:scale-[0.98] shadow-xl shadow-accent-blue/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-blue focus:ring-offset-navy-900">
            <svg class="w-5 h-5 mr-3 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            Save Changes
        </button>
    </div>
</form>

@include('admin.partials.editor-scripts')
@endsection
