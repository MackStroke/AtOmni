@extends('admin.layouts.app')
@section('title', 'Edit Job Posting')
@section('page-title', 'Edit Job Posting')
@section('content')

<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.careers.jobs.index') }}" class="p-2 -ml-2 text-text-muted hover:text-text-primary hover:bg-navy-800 rounded-xl transition-all shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title">Edit Job Posting</h1>
    </div>
</div>

<form method="POST" action="{{ route('admin.careers.jobs.update', $job) }}" class="max-w-5xl">
    @csrf
    @method('PUT')

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-5">
            <div>
                <label for="title" class="block text-sm font-medium text-text-secondary mb-1.5">Job Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $job->title) }}" required
                       class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors">
                @error('title') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="department" class="block text-sm font-medium text-text-secondary mb-1.5">Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department', $job->department) }}"
                           class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-text-secondary mb-1.5">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $job->location) }}"
                           class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-text-secondary mb-1.5">Job Description</label>
                <textarea id="description" name="description" rows="5" required
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          >{{ old('description', $job->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-text-secondary mb-1.5">Requirements</label>
                <textarea id="requirements" name="requirements" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          >{{ old('requirements', $job->requirements) }}</textarea>
            </div>

            <div>
                <label for="benefits" class="block text-sm font-medium text-text-secondary mb-1.5">Benefits</label>
                <textarea id="benefits" name="benefits" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          >{{ old('benefits', $job->benefits) }}</textarea>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="glass-card rounded-xl p-5 space-y-4">
                <h3 class="font-heading font-semibold text-text-primary text-sm">Publish Setup</h3>
                <div>
                    <label for="status" class="block text-xs font-medium text-text-muted mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="draft" {{ old('status', $job->display_status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $job->display_status) === 'published' || old('status', $job->display_status) === 'active' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="closed" {{ old('status', $job->display_status) === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                
                <div>
                    <label for="type" class="block text-xs font-medium text-text-muted mb-1">Employment Type</label>
                    <select id="type" name="type" class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="full-time" {{ old('type', $job->type) === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="part-time" {{ old('type', $job->type) === 'part-time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="contract" {{ old('type', $job->type) === 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>

                <div>
                    <label for="closing_date" class="block text-xs font-medium text-text-muted mb-1">Closing Date (Optional)</label>
                    <input type="datetime-local" id="closing_date" name="closing_date" value="{{ old('closing_date', $job->closing_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary placeholder-text-muted focus:border-electric focus:outline-none transition-colors">
                </div>
                
                <button type="submit" class="w-full px-4 py-2.5 mt-2 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                    Update Job Posting
                </button>
            </div>
            
            <div class="glass-card rounded-xl p-5 space-y-4">
                 <h3 class="font-heading font-semibold text-text-primary text-sm">Applications</h3>
                 <p class="text-3xl font-bold text-electric">{{ $job->applications()->count() }}</p>
                 <a href="{{ route('admin.careers.applications.index', ['job_posting_id' => $job->id]) }}" class="block w-full text-center px-4 py-2 rounded bg-navy-800 hover:bg-navy-700 text-white text-sm font-medium transition-colors">
                    View Applicants
                 </a>
            </div>
        </div>
    </div>
</form>

@section('scripts')
@include('admin.partials.editor-scripts')
@endsection

@endsection
