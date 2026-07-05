@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary">Profile Settings</h2>
        <p class="text-text-muted mt-1">Manage your professional identity and account security.</p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-500 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Identity & Bio Settings --}}
    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-2xl overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-text-primary mb-4">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                {{-- Profile Picture Column --}}
                <div class="md:col-span-4 flex flex-col items-center sm:items-start">
                    <label class="block text-sm font-semibold text-text-primary mb-3">Profile Picture</label>
                    <div class="relative group cursor-pointer mb-3">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-navy-800 bg-navy-900 flex items-center justify-center">
                            @if($user->profile_image)
                                <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" id="avatar-preview">
                            @else
                                <span class="text-4xl font-bold text-electric" id="avatar-placeholder">{{ substr($user->name, 0, 1) }}</span>
                                <img loading="lazy" src="" class="w-full h-full object-cover hidden" id="avatar-preview">
                            @endif
                        </div>
                        
                        <label for="profile_image" class="absolute inset-0 rounded-full bg-navy-950/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer text-white">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs font-medium">Change</span>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>
                    <p class="text-xs text-text-muted text-center sm:text-left">Recommended: 256x256px.<br>Max size: 2MB.</p>
                </div>
                
                {{-- Text Fields Column --}}
                <div class="md:col-span-8 space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-text-primary mb-1.5">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->getRawOriginal('name')) }}" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all light:bg-white light:border-slate-300">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-text-primary mb-1.5">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all light:bg-white light:border-slate-300">
                    </div>
                    
                    <div>
                        <label for="bio" class="block text-sm font-semibold text-text-primary mb-1.5">Biography</label>
                        <textarea id="bio" name="bio" rows="4" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all resize-none light:bg-white light:border-slate-300" placeholder="Write a few sentences about yourself...">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-navy-900/50 border-t border-navy-700/50 flex justify-end">
            <button type="submit" class="btn-primary">
                Save Profile
            </button>
        </div>
    </form>

    {{-- Security / Password Settings --}}
    <form action="{{ route('admin.profile.update-password') }}" method="POST" class="glass-card rounded-2xl overflow-hidden mt-8">
        @csrf
        @method('PUT')
        
        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-text-primary mb-4">Update Password</h3>
            <p class="text-sm text-text-muted mb-6">Ensure your account is using a long, random password to stay secure.</p>
            
            <div class="max-w-xl space-y-5">
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-text-primary mb-1.5">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all light:bg-white light:border-slate-300">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-semibold text-text-primary mb-1.5">New Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all light:bg-white light:border-slate-300">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-text-primary mb-1.5">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700/50 rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all light:bg-white light:border-slate-300">
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-navy-900/50 border-t border-navy-700/50 flex justify-end">
            <button type="submit" class="btn-primary">
                Update Password
            </button>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('avatar-preview');
                var placeholder = document.getElementById('avatar-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
