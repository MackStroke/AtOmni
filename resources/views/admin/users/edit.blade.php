@extends('admin.layouts.app')

@section('title', 'Edit Team Login')

@section('content')
<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.users.index') }}" class="p-2 -ml-2 text-text-muted hover:text-text-primary hover:bg-navy-800/60 rounded-xl transition-all shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title truncate">Edit Login</h1>
            <p class="text-text-muted text-xs sm:text-sm mt-1 truncate">Editing account for <span class="font-semibold text-text-secondary">{{ $user->name }}</span></p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-navy-700/30">
                <div class="w-10 h-10 rounded-xl bg-accent-blue/10 text-accent-blue flex shrink-0 items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Account Information</h2>
                    <p class="text-xs text-text-muted">Update this user's details and access level.</p>
                </div>
            </div>

            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-bold text-text-secondary mb-1.5">Full Name <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium">
                    @error('name')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-bold text-text-secondary mb-1.5">Email Address <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium">
                    @error('email')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-bold text-text-secondary mb-1.5">Access Role <span class="text-rose-400">*</span></label>
                    <select name="role" id="role" required
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium"
                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin — Full access</option>
                        <option value="editor"      {{ old('role', $user->role) === 'editor'      ? 'selected' : '' }}>Editor — Full content management</option>
                        <option value="author"      {{ old('role', $user->role) === 'author'      ? 'selected' : '' }}>Author — Create & manage own posts</option>
                        <option value="contributor" {{ old('role', $user->role) === 'contributor' ? 'selected' : '' }}>Contributor — No admin access (future use)</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="mt-1.5 text-xs text-amber-400 font-medium">⚠️ You cannot change your own role.</p>
                    @endif
                    @error('role')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Link to Team Member --}}
                <div>
                    <label for="team_member_id" class="block text-sm font-bold text-text-secondary mb-1.5">Linked Team Profile <span class="text-text-muted font-normal">(optional)</span></label>
                    <select name="team_member_id" id="team_member_id"
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium">
                        <option value="">— Not linked to a team profile —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('team_member_id', $user->team_member_id) == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->role }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_member_id')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-bold text-text-secondary mb-1.5">Bio <span class="text-text-muted font-normal">(optional)</span></label>
                    <textarea name="bio" id="bio" rows="2"
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium resize-y"
                        placeholder="Short bio…">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Optional password change --}}
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6 pb-5 border-b border-navy-700/30">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex shrink-0 items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-text-primary">Change Password</h2>
                    <p class="text-xs text-text-muted">Leave blank to keep the current password unchanged.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-bold text-text-secondary mb-1.5">New Password</label>
                    <input type="password" name="password" id="password" minlength="8"
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium"
                        placeholder="Leave blank to keep current">
                    @error('password')<p class="mt-1.5 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-text-secondary mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="block w-full px-4 py-3 rounded-xl bg-navy-950/40 border border-navy-700/20 text-text-primary placeholder-text-muted focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium"
                        placeholder="Repeat new password">
                </div>
            </div>
        </div>

        {{-- Meta info --}}
        <div class="glass-card rounded-2xl p-5 text-xs text-text-muted flex flex-wrap gap-6">
            <div>
                <span class="font-bold text-text-secondary block mb-0.5">Account Created</span>
                {{ $user->created_at->format('d M Y, h:i A') }}
            </div>
            <div>
                <span class="font-bold text-text-secondary block mb-0.5">Last Updated</span>
                {{ $user->updated_at->format('d M Y, h:i A') }}
            </div>
            <div>
                <span class="font-bold text-text-secondary block mb-0.5">User ID</span>
                #{{ $user->id }}
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-navy-700/30 text-sm font-bold text-text-muted hover:text-text-primary hover:bg-navy-800/60 transition-all text-center">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-accent-blue hover:bg-accent-blue-hover text-sm font-bold text-white transition-all shadow-lg shadow-accent-blue/25 hover:scale-[1.02] active:scale-[0.98]">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
