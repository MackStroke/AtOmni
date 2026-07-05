@extends('admin.layouts.app')
@section('title', 'Import / Export')
@section('page-title', 'Import / Export')
@section('content')

<div class="max-w-3xl space-y-6">
    {{-- Export --}}
    <div class="glass-card rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/15 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </div>
            <div class="flex-1">
                <h2 class="font-heading font-semibold text-text-primary mb-1">Export Content</h2>
                <p class="text-sm text-text-secondary mb-4">Download all posts, categories, and tags as a JSON file.</p>
                <a href="{{ route('admin.tools.do-export') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export as JSON
                </a>
            </div>
        </div>
    </div>

    {{-- Import --}}
    <div class="glass-card rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-500/15 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            </div>
            <div class="flex-1">
                <h2 class="font-heading font-semibold text-text-primary mb-1">Import Content</h2>
                <p class="text-sm text-text-secondary mb-4">Upload a JSON file to import posts. Existing posts with the same slug will be updated.</p>
                <form action="{{ route('admin.tools.do-import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="flex items-center gap-3">
                        <input type="file" name="file" accept=".json,.txt" required
                               class="flex-1 text-sm text-text-secondary file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-navy-700 file:text-text-primary hover:file:bg-navy-600 file:transition-colors file:cursor-pointer">
                        <button type="submit" class="px-4 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white text-sm font-semibold transition-all">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
