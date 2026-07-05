@extends('admin.layouts.app')
@section('title', 'Contact Queries')

@section('content')
<div class="page-header mb-6">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Contact Queries</h1>
        <p class="text-text-muted text-sm mt-1 truncate">View and manage contact form submissions.</p>
    </div>
</div>

{{-- Filters --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 hide-scrollbar chip-row">
    <a href="{{ route('admin.contacts.index') }}"
       class="shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 {{ !request('status') ? 'bg-electric/10 text-electric' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60' }}">
        All
    </a>
    <a href="{{ route('admin.contacts.index', ['status' => 'new']) }}"
       class="shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 {{ request('status') === 'new' ? 'bg-electric/10 text-electric' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60' }}">
        New
    </a>
    <a href="{{ route('admin.contacts.index', ['status' => 'read']) }}"
       class="shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 {{ request('status') === 'read' ? 'bg-electric/10 text-electric' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60' }}">
        Read
    </a>
    <a href="{{ route('admin.contacts.index', ['status' => 'replied']) }}"
       class="shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 {{ request('status') === 'replied' ? 'bg-electric/10 text-electric' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60' }}">
        Replied
    </a>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden">
    @forelse($contacts as $contact)
    <div class="glass-card rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden {{ $contact->status === 'new' ? 'border-l-4 border-l-electric bg-navy-800/40' : '' }}">
        <div class="flex justify-between items-start gap-2">
            <div class="min-w-0">
                <h3 class="font-medium text-text-primary text-sm truncate">{{ $contact->name }}</h3>
                <p class="text-xs text-text-secondary mt-0.5 truncate">{{ $contact?->subject ?? 'No Subject' }}</p>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide shrink-0
                {{ $contact->status === 'new' ? 'bg-electric/15 text-electric' : ($contact->status === 'replied' ? 'bg-success/15 text-success' : 'bg-navy-600/30 text-text-muted') }}">
                {{ $contact->status }}
            </span>
        </div>
        
        <div class="text-xs text-text-muted flex justify-between items-center mt-1">
            <span class="truncate pr-2">{{ $contact->email }}</span>
            <span class="shrink-0">{{ $contact->created_at->format('M d, Y') }}</span>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-2 pt-3 border-t border-navy-700/30">
            <a href="{{ route('admin.contacts.show', $contact) }}" 
               class="flex items-center justify-center gap-1.5 p-2.5 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </a>
            <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" class="inline-flex w-full" onsubmit="return confirm('Delete this query?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2.5 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="glass-card rounded-xl p-8 text-center text-text-muted text-sm">
        No contact queries found.
    </div>
    @endforelse
</div>

{{-- Desktop Table (Visible >= md) --}}
<div class="hidden md:block">
    <x-admin.bulk-actions resource="contacts" :actions="['delete' => 'Delete']" />
    <div class="glass-card rounded-xl overflow-hidden mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr class="text-left">
                        <th class="px-5 py-3 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="status" label="Status" /></th>
                        <th class="px-5 py-3"><x-admin.sort-header column="name" label="Name" /></th>
                        <th class="px-5 py-3 hidden sm:table-cell"><x-admin.sort-header column="email" label="Email" /></th>
                        <th class="px-5 py-3">Subject</th>
                        <th class="px-5 py-3 hidden md:table-cell"><x-admin.sort-header column="created_at" label="Date" /></th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/20">
                @if(is_countable($contacts) && count($contacts) > 0 || !is_countable($contacts) && $contacts)
                    @foreach($contacts as $contact)
                    <tr class="hover:bg-navy-800/20 transition-colors {{ $contact->status === 'new' ? 'bg-electric/[0.03]' : '' }}">
                        <td class="px-5 py-3.5 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $contact->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5">
                                @if($contact->status === 'new')
                                    <span class="w-2 h-2 rounded-full bg-electric animate-pulse"></span>
                                @endif
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                                    {{ $contact->status === 'new' ? 'bg-electric/15 text-electric' : ($contact->status === 'replied' ? 'bg-success/15 text-success' : 'bg-navy-600/30 text-text-muted') }}">
                                    {{ $contact->status }}
                                </span>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-text-primary">{{ $contact->name }}</td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-text-secondary">{{ $contact->email }}</td>
                        <td class="px-5 py-3.5 text-text-secondary truncate max-w-xs">{{ $contact?->subject ?? '—' }}</td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-text-muted">{{ $contact->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="p-1.5 rounded-lg text-text-muted hover:text-electric hover:bg-electric/10 transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-800" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}" class="inline" onsubmit="return confirm('Delete this query?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg text-text-muted hover:text-alert-red hover:bg-alert-red/10 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-navy-800" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-text-muted">No contact queries found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>

@if($contacts->hasPages())
    <div class="mt-6 flex justify-center">{{ $contacts->links() }}</div>
@endif

@endsection
