@extends('admin.layouts.app')
@section('title', 'Newsletter Subscribers')

@section('content')

<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary">Newsletter Subscribers</h1>
        <p class="text-sm text-text-muted mt-1">Manage your email newsletter subscribers.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <span class="text-sm text-text-muted shrink-0 hidden sm:inline">{{ $subscribers->total() }} total</span>
        <form method="GET" action="{{ route('admin.newsletter.index') }}" class="relative w-full sm:w-auto">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" aria-label="Search" name="search" value="{{ request('search') }}" placeholder="Search by email…"
                   class="w-full sm:w-64 pl-9 pr-4 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric transition-colors">
        </form>
    </div>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden">
    @forelse($subscribers as $sub)
    <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden">
        <div class="flex justify-between items-start gap-2">
            <div class="flex-1 min-w-0">
                <h3 class="font-medium text-text-primary text-sm truncate">{{ $sub->email }}</h3>
                <p class="text-xs text-text-muted mt-0.5 truncate">Subscribed: {{ $sub->subscribed_at?->format('M d, Y') ?? $sub->created_at->format('M d, Y') }}</p>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-navy-800 text-text-muted shrink-0">#{{ $sub->id }}</span>
        </div>

        <div class="border-t border-navy-700/30 pt-3 mt-1">
            <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" class="w-full" onsubmit="return confirm('Remove this subscriber?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Remove
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="glass-card rounded-xl p-8 text-center text-text-muted text-sm">
        No subscribers yet.
    </div>
    @endforelse
</div>

<div class="hidden md:block">
    <x-admin.bulk-actions resource="newsletter" :actions="['delete' => 'Delete']" />
    <div class="glass-card rounded-xl overflow-hidden mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr class="text-left">
                        <th class="px-5 py-3 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="id" label="#" /></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="email" label="Email" /></th>
                        <th class="px-5 py-3 hidden sm:table-cell"><x-admin.sort-header column="created_at" label="Subscribed On" /></th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/20">
                @if(is_countable($subscribers) && count($subscribers) > 0 || !is_countable($subscribers) && $subscribers)
                    @foreach($subscribers as $sub)
                    <tr class="hover:bg-navy-800/20 transition-colors">
                        <td class="px-5 py-3.5 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $sub->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-5 py-3.5 text-text-muted">{{ $sub->id }}</td>
                        <td class="px-5 py-3.5 font-medium text-text-primary">{{ $sub->email }}</td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-text-secondary">{{ $sub->subscribed_at?->format('M d, Y') ?? $sub->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" class="inline" onsubmit="return confirm('Remove this subscriber?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 rounded-lg text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-navy-800" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-text-muted">No subscribers yet.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>

@if($subscribers->hasPages())
    <div class="mt-6 flex justify-center">{{ $subscribers->links() }}</div>
@endif

@endsection
