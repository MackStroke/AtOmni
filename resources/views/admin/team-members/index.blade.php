@extends('admin.layouts.app')

@section('title', 'Team Members')

@section('content')
<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary">Team Members</h1>
        <p class="text-sm text-text-muted mt-1">Manage team member profiles and information.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.team-members.create') }}" class="btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden sm:inline">New Member</span>
        </a>
    </div>
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden">
    @if(isset($members) && $members->count() > 0)
        @foreach($members as $member)
        <div class="glass-card rounded-xl p-4 flex flex-col relative overflow-hidden">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-navy-800 shrink-0">
                    @if($member->photo_path)
                        <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($member->photo_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-electric text-white font-bold text-lg">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-text-primary text-base truncate">{{ $member->name }}</h3>
                    <p class="text-sm text-text-muted truncate">{{ $member->role }}</p>
                </div>
                <div class="shrink-0">
                    @if($member->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500">Active</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-navy-900/60 text-text-muted">Inactive</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-navy-700/30">
                <a href="{{ route('admin.team-members.edit', $member) }}" 
                   class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
                <form action="{{ route('admin.team-members.destroy', $member) }}" method="POST" class="w-full" onsubmit="return confirm('Delete this team member?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-alert-red hover:bg-alert-red/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="glass-card rounded-xl p-8 text-center text-text-muted">No team members found.</div>
    @endif
</div>

<div class="hidden md:block">
    <x-admin.bulk-actions resource="team-members" :actions="['delete' => 'Delete']" />
    <div class="glass-card rounded-2xl overflow-hidden mb-6 mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th scope="col" class="px-6 py-4 font-bold w-20">Photo</th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="name" label="Name" /></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="role" label="Role" /></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="is_active" label="Status" /></th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/10">
                @if(isset($members) && $members->count() > 0)
                    @foreach($members as $member)
                    <tr class="transition-colors hover:bg-navy-950/30 group">
                        <td class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $member->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-navy-800">
                                @if($member->photo_path)
                                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($member->photo_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-electric text-white font-bold text-lg">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-text-primary text-base">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-text-muted">{{ $member->role }}</td>
                        <td class="px-6 py-4">
                            @if($member->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 shadow-sm">Active</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-navy-900/60 text-text-muted border border-navy-700/30 shadow-sm">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.team-members.edit', $member) }}" class="p-2 text-text-muted hover:text-accent-blue hover:bg-accent-blue/10 transition-all rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-blue" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.team-members.destroy', $member) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this team member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500/70 hover:text-rose-500 hover:bg-rose-500/10 transition-all rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-text-muted italic">No team members found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
