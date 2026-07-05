@extends('admin.layouts.app')

@section('title', 'Team Logins')

@section('content')
<div class="page-header mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex-1 min-w-0">
        <h1 class="text-xl sm:text-3xl font-bold tracking-tight text-text-primary truncate">Team Logins</h1>
        <p class="text-text-muted text-xs sm:text-sm mt-1 truncate">Manage admin panel access for your team members.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
        <a href="{{ route('admin.users.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-sm font-bold tracking-wide text-white transition-all duration-300 rounded-xl bg-accent-blue hover:bg-accent-blue-hover hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-blue focus:ring-offset-navy-900 shadow-xl shadow-accent-blue/20 whitespace-nowrap">
            <svg class="w-5 h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span class="inline">Add Login</span>
        </a>
    </div>
</div>

{{-- Role filter tabs --}}
<div class="chip-row mb-6">
    @foreach(['' => 'All Users', 'super_admin' => 'Super Admin', 'editor' => 'Editor', 'author' => 'Author', 'contributor' => 'Contributor'] as $role => $label)
        <a href="{{ route('admin.users.index', $role ? ['role' => $role] : []) }}"
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all shrink-0
           {{ request('role', '') === $role
               ? 'bg-accent-blue text-white shadow-lg shadow-accent-blue/25'
               : 'bg-navy-800/60 text-text-muted hover:text-text-primary hover:bg-navy-700/80 border border-navy-700/30' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Mobile Cards (Visible < md) --}}
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @forelse($users as $user)
    <div class="glass-card rounded-xl p-4 flex flex-col relative overflow-hidden {{ $user->trashed() ? 'opacity-60' : '' }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-navy-800 shrink-0">
                @if($user->profile_image)
                    <img loading="lazy" src="{{ \Illuminate\Support\Str::startsWith($user->profile_image, 'http') ? $user->profile_image : \Illuminate\Support\Facades\Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-electric/20 text-electric font-bold text-base">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-text-primary text-sm truncate">{{ $user->name }}</h3>
                <p class="text-xs text-text-muted truncate">{{ $user->email }}</p>
            </div>
        </div>
        <div class="mt-3 flex items-center justify-between">
            @php
                $roleColors = [
                    'super_admin'  => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                    'editor'       => 'bg-accent-blue/10 text-accent-blue border-accent-blue/20',
                    'author'       => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    'contributor'  => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                ];
                $colorClass = $roleColors[$user->role] ?? 'bg-navy-700/30 text-text-muted border-navy-600/30';
            @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $colorClass }}">
                {{ \App\Models\User::roleLabel($user->role) }}
            </span>
            @if($user->trashed())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-400 border border-rose-500/20">Revoked</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
            @endif
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-navy-700/30">
            @if($user->trashed())
                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="col-span-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                        Restore Access
                    </button>
                </form>
            @else
                <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-electric hover:bg-electric/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <button type="button" onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')" class="flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-amber-400 hover:bg-amber-400/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </button>
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Revoke access for {{ addslashes($user->name) }}?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 p-2 rounded-lg text-xs font-medium bg-navy-800/50 text-text-secondary hover:text-rose-500 hover:bg-rose-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </button>
                </form>
                @else
                <div class="p-2"></div>
                @endif
            @endif
        </div>
    </div>
    @empty
    <div class="glass-card rounded-xl p-8 text-center text-text-muted">No users found.</div>
    @endforelse
</div>

{{-- Desktop Table (Visible >= md) --}}
<div class="hidden md:block">
    <x-admin.bulk-actions resource="users" :actions="['delete' => 'Revoke Access']" />
    <div class="glass-card rounded-2xl overflow-hidden mb-6 mt-3">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-navy-800/50 light:bg-slate-100 text-text-secondary uppercase text-xs font-semibold tracking-wider border-b border-navy-700/30 light:border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select all" class="bulk-master-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></th>
                        <th scope="col" class="px-6 py-4 font-bold w-16">Avatar</th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="name" label="Name & Email" /></th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="role" label="Role" /></th>
                        <th scope="col" class="px-6 py-4 font-bold">Team Profile</th>
                        <th scope="col" class="px-6 py-4 font-bold"><x-admin.sort-header column="created_at" label="Created" /></th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
            <tbody class="divide-y divide-navy-700/10">
                @forelse($users as $user)
                <tr class="transition-colors hover:bg-navy-950/30 group {{ $user->trashed() ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4 w-10 text-center"><input type="checkbox" aria-label="Select item" value="{{ $user->id }}" class="bulk-item-checkbox rounded border-navy-700/50 bg-navy-800/50 text-electric focus:ring-electric"></td>
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-navy-800 shrink-0">
                            @if($user->profile_image)
                                <img loading="lazy" src="{{ \Illuminate\Support\Str::startsWith($user->profile_image, 'http') ? $user->profile_image : \Illuminate\Support\Facades\Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-electric/20 text-electric font-bold text-base">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block font-bold text-text-primary">{{ $user->name }}</span>
                        <span class="block text-xs text-text-muted mt-0.5">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $roleColors = [
                                'super_admin'  => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                'editor'       => 'bg-accent-blue/10 text-accent-blue border-accent-blue/20',
                                'author'       => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'contributor'  => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            ];
                            $colorClass = $roleColors[$user->role] ?? 'bg-navy-700/30 text-text-muted border-navy-600/30';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border {{ $colorClass }}">
                            {{ \App\Models\User::roleLabel($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-text-muted text-xs">
                        @if($user->teamMember)
                            <span class="font-medium text-text-secondary">{{ $user->teamMember->name }}</span>
                        @else
                            <span class="text-navy-600 italic">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-text-muted text-xs">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($user->trashed())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-400 border border-rose-500/20">Revoked</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($user->trashed())
                                {{-- Restore --}}
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-lg transition-all" title="Restore Access">
                                        Restore
                                    </button>
                                </form>
                            @else
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-text-muted hover:text-accent-blue hover:bg-accent-blue/10 transition-all rounded-xl" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                {{-- Reset Password --}}
                                <button
                                    type="button"
                                    onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="p-2 text-text-muted hover:text-amber-400 hover:bg-amber-400/10 transition-all rounded-xl"
                                    title="Reset Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>

                                {{-- Delete (revoke) --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Revoke access for {{ addslashes($user->name) }}? They will no longer be able to log in.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500/70 hover:text-rose-500 hover:bg-rose-500/10 transition-all rounded-xl" title="Revoke Access">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center text-text-muted italic">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- Pagination --}}
@if($users->hasPages())
<div class="mt-4">
    {{ $users->links() }}
</div>
@endif

{{-- Reset Password Modal --}}
<div id="reset-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeResetModal()"></div>
    <div class="relative w-full max-w-md mx-4 glass-card rounded-2xl p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-text-primary">Reset Password</h3>
                <p class="text-xs text-text-muted mt-0.5">Setting new password for <span id="reset-modal-name" class="font-semibold text-text-secondary"></span></p>
            </div>
            <button onclick="closeResetModal()" class="ml-auto p-1.5 text-text-muted hover:text-text-primary rounded-lg hover:bg-navy-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="reset-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="modal-password" class="block text-sm font-bold text-text-secondary mb-1.5">New Password</label>
                <input type="password" name="password" id="modal-password" required minlength="8" class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all font-medium" placeholder="Min 8 chars, include letters & numbers">
            </div>
            <div>
                <label for="modal-password-confirm" class="block text-sm font-bold text-text-secondary mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" id="modal-password-confirm" required class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all font-medium" placeholder="Repeat password">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeResetModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-navy-700/30 text-sm font-bold text-text-muted hover:text-text-primary hover:bg-navy-800/60 transition-all">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-sm font-bold text-navy-950 transition-all shadow-lg shadow-amber-500/20">Reset Password</button>
            </div>
        </form>
    </div>
</div>

{{-- Access Control Info Card --}}
<div class="glass-card rounded-2xl p-6 mt-4">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-lg bg-accent-blue/10 text-accent-blue flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="font-bold text-text-primary text-sm">Role Access Reference</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['role' => 'Super Admin', 'color' => 'purple', 'perms' => ['Full admin access', 'Manage all users', 'All settings', 'All content']],
            ['role' => 'Editor',      'color' => 'blue',   'perms' => ['Create & edit posts', 'Manage pages', 'Media library', 'Reports']],
            ['role' => 'Author',      'color' => 'emerald','perms' => ['Create own posts', 'Upload media', 'Edit own posts', 'View dashboard']],
            ['role' => 'Contributor', 'color' => 'amber',  'perms' => ['No admin access', 'For future use', '—', '—']],
        ] as $info)
        <div class="p-4 rounded-xl bg-navy-950/40 border border-navy-700/20">
            <span class="text-xs font-bold uppercase tracking-wider text-{{ $info['color'] }}-400 block mb-2">{{ $info['role'] }}</span>
            <ul class="space-y-1">
                @foreach($info['perms'] as $perm)
                <li class="text-xs text-text-muted flex items-center gap-1.5">
                    @if($perm !== '—')
                        <svg class="w-3 h-3 text-{{ $info['color'] }}-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-3 h-3 text-navy-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    @endif
                    {{ $perm }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
const resetRouteBase = "{{ url('/admin/users') }}";

function openResetModal(userId, userName) {
    document.getElementById('reset-modal-name').textContent = userName;
    document.getElementById('reset-form').action = `${resetRouteBase}/${userId}/reset-password`;
    document.getElementById('modal-password').value = '';
    document.getElementById('modal-password-confirm').value = '';
    document.getElementById('reset-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('modal-password').focus(), 100);
}

function closeResetModal() {
    document.getElementById('reset-modal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeResetModal();
});
</script>
@endsection
