@extends('admin.layouts.app')
@section('title', 'Job Postings')
@section('page-title', 'Job Postings')
@section('content')

{{-- Toolbar --}}
<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex-1 min-w-0">
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary truncate">Job Postings</h1>
    </div>
    <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
        <a href="{{ route('admin.careers.jobs.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-xl bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-electric focus:ring-offset-navy-900 shadow-lg shadow-electric/20 whitespace-nowrap">
            <svg class="w-5 h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="inline">New Job Posting</span>
        </a>
    </div>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @forelse($posts as $job)
    <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden">
        <div class="flex justify-between items-start gap-2">
            <div class="flex-1 min-w-0">
                <h3 class="font-medium text-text-primary text-sm truncate">{{ $job->title }}</h3>
                <p class="text-xs text-text-muted mt-0.5 truncate">{{ $job?->department ?? '—' }} &bull; {{ $job?->location ?? '—' }}</p>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide shrink-0
                {{ $job->display_status === 'published' ? 'bg-success/15 text-success' : ($job->display_status === 'closed' || $job->display_status === 'expired' ? 'bg-alert-red/15 text-alert-red' : 'bg-navy-600/30 text-text-muted') }}">
                {{ $job->display_status }}
            </span>
        </div>
        <div class="flex items-center gap-2 text-xs text-text-secondary mt-1">
            <span class="px-2 py-0.5 rounded bg-navy-700 text-text-muted">{{ ucfirst($job->type) }}</span>
            <span>&bull;</span>
            <a href="{{ route('admin.careers.applications.index', ['job_posting_id' => $job->id]) }}" class="text-electric hover:underline font-medium">
                {{ $job->applications()->count() }} Applications
            </a>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-navy-700/30">
            <a href="{{ route('admin.careers.jobs.edit', $job) }}" class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.careers.jobs.destroy', $job) }}" class="w-full" onsubmit="return confirm('Delete this job posting?')">
                @csrf @method('DELETE')
                <button class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="glass-card rounded-xl p-8 text-center text-text-muted">No job postings found.</div>
    @endforelse
</div>

{{-- Jobs Table --}}
<div class="hidden md:block">
    <x-admin.bulk-actions resource="jobs" :actions="['delete' => 'Delete', 'draft' => 'Draft', 'publish' => 'Publish']" />
    <div class="glass-card rounded-xl overflow-hidden mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr class="text-left">
                        <th class="px-5 py-3 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="title" label="Title" /></th>
                        <th class="px-5 py-3 hidden sm:table-cell"><x-admin.sort-header column="department" label="Department" /></th>
                        <th class="px-5 py-3 hidden md:table-cell"><x-admin.sort-header column="location" label="Location" /></th>
                        <th class="px-5 py-3 hidden lg:table-cell"><x-admin.sort-header column="type" label="Type" /></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="status" label="Status" /></th>
                        <th class="px-5 py-3 hidden xl:table-cell">Applications</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/20">
                @if ($posts->isEmpty())
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-text-muted">No job postings found. <a href="{{ route('admin.careers.jobs.create') }}" class="text-electric hover:text-electric-light focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 rounded">Create one →</a></td>
                    </tr>
                @else
                    @foreach($posts as $job)
                    <tr class="hover:bg-navy-800/20 transition-colors">
                        <td class="px-5 py-3.5 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $job->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-text-primary">{{ $job->title }}</p>
                        </td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-text-secondary">{{ $job?->department ?? '—' }}</td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-text-secondary">{{ $job?->location ?? '—' }}</td>
                        <td class="px-5 py-3.5 hidden lg:table-cell">
                            <span class="px-2 py-0.5 rounded text-xs bg-navy-700 text-text-muted">{{ ucfirst($job->type) }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                                {{ $job->display_status === 'published' ? 'bg-success/15 text-success' : ($job->display_status === 'closed' || $job->display_status === 'expired' ? 'bg-alert-red/15 text-alert-red' : 'bg-navy-600/30 text-text-muted') }}">
                                {{ $job->display_status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 hidden xl:table-cell">
                            <a href="{{ route('admin.careers.applications.index', ['job_posting_id' => $job->id]) }}" class="text-electric hover:underline font-medium">
                                {{ $job->applications()->count() }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.careers.jobs.edit', $job) }}" class="p-1.5 rounded-lg text-text-muted hover:text-electric hover:bg-electric/10 transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-800" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.careers.jobs.destroy', $job) }}" class="inline" onsubmit="return confirm('Delete this job posting?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-navy-800" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Pagination --}}
@if($posts->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $posts->links() }}
    </div>
@endif

@endsection
