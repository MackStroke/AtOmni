@extends('admin.layouts.app')

@section('title', 'Edit Donor')
@section('page-title', 'Edit Donor')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.donors.index') }}" class="text-text-muted hover:text-accent-blue transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold tracking-tight text-text-primary">Edit {{ $donor->name }}</h2>
    </div>
    <p class="text-text-muted mt-2 ml-9">Update details for this supporter.</p>
</div>

<div class="max-w-4xl glass-card rounded-2xl overflow-hidden border border-navy-700/30 p-6 md:p-8">
    <form action="{{ route('admin.donors.update', $donor) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col md:flex-row gap-8 mb-6">
            <div class="shrink-0 space-y-3">
                <div class="w-32 h-32 rounded-2xl bg-navy-800 border-2 border-dashed border-navy-600 flex items-center justify-center overflow-hidden mx-auto md:mx-0 shadow-inner group relative">
                    @if($donor->image_path)
                        <img id="image_preview" src="{{ \Illuminate\Support\Facades\Storage::url($donor->image_path) }}" alt="{{ $donor->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-navy-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="text-xs font-bold text-white tracking-widest uppercase">Change</span>
                        </div>
                    @else
                        <span id="image_placeholder" class="text-5xl font-bold text-navy-500">{{ substr($donor->name, 0, 1) }}</span>
                        <img id="image_preview" src="" class="hidden w-full h-full object-cover">
                    @endif
                </div>
            </div>
            
            <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2 md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-text-secondary">Donor Name <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $donor->name) }}" required class="form-input">
                    @error('name')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>
                
                <div class="space-y-2">
                    <label for="amount" class="block text-sm font-semibold text-text-secondary">Donation Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', floatval($donor->amount)) }}" class="form-input">
                    @error('amount')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="donated_at" class="block text-sm font-semibold text-text-secondary">Donation Date</label>
                    <input type="date" name="donated_at" id="donated_at" value="{{ old('donated_at', $donor->donated_at ? $donor->donated_at->format('Y-m-d') : '') }}" class="form-input">
                    @error('donated_at')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-navy-700/30">
            <div class="space-y-2 md:col-span-2">
                <label for="social_link" class="block text-sm font-semibold text-text-secondary">Social Link (Insta/FB)</label>
                <input type="url" name="social_link" id="social_link" value="{{ old('social_link', $donor->social_link) }}" placeholder="https://instagram.com/..." class="form-input">
                @error('social_link')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2 md:col-span-2">
                <label for="message" class="block text-sm font-semibold text-text-secondary">Personal Message (Optional)</label>
                <textarea name="message" id="message" rows="3" class="form-input resize-none">{{ old('message', $donor->message) }}</textarea>
                @error('message')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2 md:col-span-1">
                <label for="image" class="block text-sm font-semibold text-text-secondary">Replace Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="form-input !py-2" onchange="previewImage(this)">
                @error('image')<p class="mt-1 text-sm text-rose-400 font-medium">{{ $message }}</p>@enderror
            </div>
            
            <div class="space-y-2 md:col-span-1 border-l border-navy-700/30 pl-6">
                <label class="block text-sm font-semibold text-text-secondary mb-4">Settings</label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', $donor->is_active) ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-navy-700 border border-navy-600 rounded-full peer-checked:bg-accent-blue peer-checked:border-accent-blue transition-colors relative">
                            <div class="absolute left-1.5 top-1 font-bold text-white text-[9px] opacity-0 peer-checked:opacity-100 transition-opacity">ON</div>
                            <div class="w-3.5 h-3.5 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition-transform shadow-sm"></div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-text-primary group-hover:text-accent-blue transition-colors">Visible on Donate Page</span>
                </label>
            </div>
        </div>

        <div class="pt-6 border-t border-navy-700/30 flex justify-end gap-4">
            <a href="{{ route('admin.donors.index') }}" class="btn-primary bg-navy-800 text-text-primary hover:bg-navy-700">Cancel</a>
            <button type="submit" class="btn-primary">Update Donor</button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image_preview').src = e.target.result;
            document.getElementById('image_preview').classList.remove('hidden');
            if(document.getElementById('image_placeholder')) {
                document.getElementById('image_placeholder').classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
