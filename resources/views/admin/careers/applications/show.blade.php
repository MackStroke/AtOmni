@extends('admin.layouts.app')
@section('title', 'Application Details')
@section('page-title', 'Application: ' . $application->first_name . ' ' . $application->last_name)
@section('content')

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.careers.applications.index') }}" class="text-text-muted hover:text-electric flex items-center gap-1 text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Applications
    </a>
    
    <span class="text-sm text-text-muted">Applied: {{ $application->created_at->format('M d, Y h:i A') }}</span>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Main content: Applicant Details --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="glass-card rounded-xl p-6">
            <h3 class="font-heading font-semibold text-lg text-text-primary mb-4 border-b border-navy-700/50 pb-2">Applicant Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs text-text-muted uppercase tracking-wider mb-1">Full Name</span>
                    <p class="text-text-primary font-medium">{{ $application->first_name }} {{ $application->last_name }}</p>
                </div>
                <div>
                    <span class="block text-xs text-text-muted uppercase tracking-wider mb-1">Email Address</span>
                    <a href="mailto:{{ $application->email }}" class="text-electric hover:underline font-medium">{{ $application->email }}</a>
                </div>
                <div>
                    <span class="block text-xs text-text-muted uppercase tracking-wider mb-1">Phone Number</span>
                    <p class="text-text-primary font-medium">{{ $application?->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="block text-xs text-text-muted uppercase tracking-wider mb-1">Portfolio / Website</span>
                    @if($application->portfolio_url)
                        <a href="{{ $application->portfolio_url }}" target="_blank" class="text-electric hover:underline font-medium break-all">{{ $application->portfolio_url }}</a>
                    @else
                        <p class="text-text-primary font-medium">N/A</p>
                    @endif
                </div>
            </div>
        </div>

        @if($application->cover_letter)
            <div class="glass-card rounded-xl p-6">
                <h3 class="font-heading font-semibold text-lg text-text-primary mb-4 border-b border-navy-700/50 pb-2">Cover Letter</h3>
                <div class="prose prose-invert max-w-none text-text-secondary text-sm whitespace-pre-wrap">
                    {{ $application->cover_letter }}
                </div>
            </div>
        @endif
    </div>

    {{-- Sidebar: Status & Processing --}}
    <div class="space-y-6">
        <div class="glass-card rounded-xl p-6 space-y-4">
            <h3 class="font-heading font-semibold text-text-primary text-sm border-b border-navy-700/50 pb-2">Application Status</h3>
            
            <form method="POST" action="{{ route('admin.careers.applications.update', $application) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="status" class="block text-xs font-medium text-text-muted mb-1.5">Current Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="new" {{ $application->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="reviewing" {{ $application->status === 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                        <option value="interviewing" {{ $application->status === 'interviewing' ? 'selected' : '' }}>Interviewing</option>
                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="hired" {{ $application->status === 'hired' ? 'selected' : '' }}>Hired</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                    Update Status
                </button>
            </form>
        </div>

        <div class="glass-card rounded-xl p-6 space-y-4">
            <h3 class="font-heading font-semibold text-text-primary text-sm border-b border-navy-700/50 pb-2">Associated Job</h3>
            <div class="pt-1">
                <p class="font-medium text-text-primary">{{ $application->jobPosting?->title ?? 'Unknown Job' }}</p>
                @if($application->jobPosting)
                    <p class="text-xs text-text-muted mt-1">{{ $application->jobPosting->department }} &bull; {{ $application->jobPosting->location }}</p>
                    <a href="{{ route('admin.careers.jobs.edit', $application->jobPosting) }}" class="inline-block text-xs text-electric hover:underline mt-2">View Job Posting &rarr;</a>
                @endif
            </div>
        </div>

        <div class="glass-card rounded-xl p-6 space-y-4">
            <h3 class="font-heading font-semibold text-text-primary text-sm border-b border-navy-700/50 pb-2">Resume Document</h3>
            @if($application->resume_path)
                <div class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/50 border border-navy-700/50">
                    <div class="w-10 h-10 rounded bg-electric/10 text-electric flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-text-primary truncate" title="{{ basename($application->resume_path) }}">
                            {{ basename($application->resume_path) }}
                        </p>
                        <p class="text-[10px] text-text-muted mt-0.5">Uploaded File</p>
                    </div>
                </div>
                <a href="{{ route('admin.careers.applications.download-resume', $application) }}" target="_blank" class="flex items-center justify-center w-full gap-2 px-4 py-2 mt-2 rounded-lg bg-navy-800 hover:bg-navy-700 text-text-primary border border-navy-700/50 text-sm font-medium transition-all shadow-sm">
                    <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Resume
                </a>
            @else
                <p class="text-sm text-text-muted italic">No resume file uploaded.</p>
            @endif
        </div>
    </div>
</div>

@endsection
