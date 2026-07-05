@extends('admin.layouts.app')

@section('title', 'Manage Donors')
@section('page-title', 'Donors')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary">Donor Shoutouts</h2>
        <p class="text-text-muted mt-1">Manage supporters to feature on the donation wall.</p>
    </div>
    <a href="{{ route('admin.donors.create') }}" class="btn-primary inline-flex sm:w-auto">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Donor
    </a>
</div>

<x-admin.bulk-actions resource="donors" :actions="['delete' => 'Delete']" />
<div class="glass-card rounded-2xl overflow-hidden border border-navy-700/30 mt-3">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                <tr>
                    <th class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                    <th class="px-6 py-4"><x-admin.sort-header column="name" label="Donor" /></th>
                    <th class="px-6 py-4"><x-admin.sort-header column="amount" label="Amount" /></th>
                    <th class="px-6 py-4"><x-admin.sort-header column="is_active" label="Status" /></th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-navy-700/20">
                @forelse($donors as $donor)
                <tr class="hover:bg-navy-800/30 transition-colors group">
                    <td class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $donor->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-navy-800 border border-navy-700/50 flex items-center justify-center overflow-hidden shrink-0">
                                @if($donor->image_path)
                                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($donor->image_path) }}" alt="{{ $donor->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg font-bold text-accent-blue">{{ substr($donor->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-text-primary">{{ $donor->name }}</h3>
                                @if($donor->donated_at)
                                <p class="text-xs text-text-muted">{{ $donor->donated_at->format('M d, Y') }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium text-text-secondary">
                           {{ $donor->amount ? '₹' . number_format($donor->amount, 2) : 'N/A' }} 
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($donor->is_active)
                            <span class="badge-primary bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Active</span>
                        @else
                            <span class="badge-primary bg-slate-500/10 text-slate-400 border-slate-500/20">Hidden</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.donors.edit', $donor) }}" class="p-2 text-text-muted hover:text-accent-blue hover:bg-accent-blue/10 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('admin.donors.destroy', $donor) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this donor?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-text-muted hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-text-muted">
                        No donors added yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
