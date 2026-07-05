@extends('admin.layouts.app')

@section('title', 'Add Team Member')

@section('content')
<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.team-members.index') }}" class="p-2 -ml-2 text-text-muted hover:text-text-primary hover:bg-navy-800 rounded-xl transition-all shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title">Add Team Member</h1>
    </div>
</div>

<div class="max-w-2xl">
    <form action="{{ route('admin.team-members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="glass-card rounded-2xl p-4 sm:p-6 md:p-8 space-y-6">
            <div>
                <label for="name" class="block text-sm font-semibold text-text-primary mb-2">Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors" value="{{ old('name') }}">
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-text-primary mb-2">Role/Title <span class="text-rose-500">*</span></label>
                <input type="text" name="role" id="role" required class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors" value="{{ old('role') }}">
            </div>

            <div>
                <label for="photo" class="block text-sm font-semibold text-text-primary mb-2">Photo</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-text-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-electric/10 file:text-electric hover:file:bg-electric/20 transition-colors">
            </div>

            <div>
                <label for="bio" class="block text-sm font-semibold text-text-primary mb-2">Short Bio</label>
                <textarea name="bio" id="bio" rows="4" class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">{{ old('bio') }}</textarea>
            </div>
            
            <div>
                <label for="order_column" class="block text-sm font-semibold text-text-primary mb-2">Sort Order</label>
                <input type="number" name="order_column" id="order_column" value="{{ old('order_column', 0) }}" class="w-full px-4 py-3 rounded-lg bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-5 h-5 rounded bg-navy-900 border-navy-700 text-electric focus:ring-electric focus:ring-offset-navy-800">
                <label for="is_active" class="text-sm font-semibold text-text-primary">Active (Visible)</label>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_founding_member" id="is_founding_member" value="1" class="w-5 h-5 rounded bg-navy-900 border-navy-700 text-electric focus:ring-electric focus:ring-offset-navy-800">
                <div>
                    <label for="is_founding_member" class="text-sm font-semibold text-text-primary">Founding Member</label>
                    <p class="text-xs text-text-muted mt-0.5">Displays a special "Origin Story / 2025 Legend" badge on their profile picture in the About Us page.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.team-members.index') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-bold text-text-secondary hover:text-text-primary transition-colors text-center">Cancel</a>
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-bold transition-all shadow-lg shadow-electric/20">
                Save Team Member
            </button>
        </div>
    </form>
</div>
@endsection

