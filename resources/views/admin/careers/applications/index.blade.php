@extends('admin.layouts.app')
@section('title', 'Job Applications')
@section('page-title', 'Job Applications')
@section('content')

{{-- Toolbar --}}
<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex-1 min-w-0">
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary truncate">Job Applications</h1>
    </div>
    <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
        <form method="GET" action="{{ route('admin.careers.applications.index') }}" class="flex items-center w-full">
            <select name="job_posting_id" onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-secondary focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors appearance-none pr-8 relative">
                <option value="">All Jobs</option>
                @foreach(\App\Models\JobPosting::orderBy('title')->get() as $jp)
                    <option value="{{ $jp->id }}" {{ request('job_posting_id') == $jp->id ? 'selected' : '' }}>{{ $jp->title }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @forelse($applications as $app)
    <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden">
        <div class="flex justify-between items-start gap-2">
            <div class="flex-1 min-w-0">
                <h3 class="font-medium text-text-primary text-sm truncate">{{ $app->first_name }} {{ $app->last_name }}</h3>
                <p class="text-xs text-text-muted mt-0.5 truncate">{{ $app->email }}</p>
            </div>
            @php
                $statusColors = [
                    'new' => 'bg-electric/15 text-electric',
                    'reviewing' => 'bg-amber/15 text-amber',
                    'interviewing' => 'bg-purple-500/15 text-purple-400',
                    'rejected' => 'bg-alert-red/15 text-alert-red',
                    'hired' => 'bg-success/15 text-success',
                ];
                $colorClass = $statusColors[$app->status] ?? 'bg-navy-600/30 text-text-muted';
            @endphp
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide shrink-0 {{ $colorClass }}">
                {{ $app->status }}
            </span>
        </div>
        <div class="flex flex-col gap-1 text-xs mt-1">
            <div class="flex items-center text-text-secondary gap-1.5 min-w-0">
                <svg class="w-3.5 h-3.5 shrink-0 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="truncate">{{ $app->jobPosting?->title ?? 'Unknown Job' }}</span>
            </div>
            <div class="flex items-center text-text-muted gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Applied {{ $app->created_at->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-navy-700/30">
            <a href="{{ route('admin.careers.applications.show', $app) }}" class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </a>
            <form method="POST" action="{{ route('admin.careers.applications.destroy', $app) }}" class="w-full" onsubmit="return confirm('Delete this application?')">
                @csrf @method('DELETE')
                <button class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="glass-card rounded-xl p-8 text-center text-text-muted">No applications found.</div>
    @endforelse
</div>

{{-- Applications Table --}}
<div class="hidden md:block">
    <x-admin.bulk-actions resource="applications" :actions="['delete' => 'Delete']" />
    <div class="glass-card rounded-xl overflow-hidden mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr class="text-left">
                        <th class="px-5 py-3 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="first_name" label="Applicant" /></th>
                        <th class="px-5 py-3 hidden sm:table-cell">Job Role</th>
                        <th class="px-5 py-3 hidden md:table-cell"><x-admin.sort-header column="email" label="Email" /></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="status" label="Status" /></th>
                        <th class="px-5 py-3 hidden lg:table-cell"><x-admin.sort-header column="created_at" label="Applied Date" /></th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/20">
                @if ($applications->isEmpty())
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-text-muted">No applications found.</td>
                    </tr>
                @else
                    @foreach ($applications as $app)
                    <tr class="hover:bg-navy-800/20 transition-colors">
                        <td class="px-5 py-3.5 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $app->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-text-primary">{{ $app->first_name }} {{ $app->last_name }}</p>
                        </td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-text-secondary">
                            {{ $app->jobPosting?->title ?? 'Unknown Job' }}
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-text-secondary">{{ $app->email }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusColors = [
                                    'new' => 'bg-electric/15 text-electric',
                                    'reviewing' => 'bg-amber/15 text-amber',
                                    'interviewing' => 'bg-purple-500/15 text-purple-400',
                                    'rejected' => 'bg-alert-red/15 text-alert-red',
                                    'hired' => 'bg-success/15 text-success',
                                ];
                                $colorClass = $statusColors[$app->status] ?? 'bg-navy-600/30 text-text-muted';
                            @endphp
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide {{ $colorClass }}">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 hidden lg:table-cell text-text-muted">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.careers.applications.show', $app) }}" class="p-1.5 rounded-lg text-text-muted hover:text-electric hover:bg-electric/10 transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-800" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.careers.applications.destroy', $app) }}" class="inline" onsubmit="return confirm('Delete this application?')">
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
@if($applications->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $applications->links() }}
    </div>
@endif

@endsection
