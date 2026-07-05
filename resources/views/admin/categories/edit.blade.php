@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-center w-10 h-10 transition-colors rounded-full bg-navy-800 text-text-muted hover:text-text-primary hover:bg-navy-700 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title">Edit Category</h1>
    </div>
</div>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" class="max-w-2xl space-y-6">
    @csrf
    @method('PUT')

    <div class="p-6 shadow-xl bg-navy-800 border border-navy-700/50 rounded-xl">
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="name" class="block text-sm font-medium text-text-secondary">Category Name</label>
                    <button type="button" onclick="autoFillCategory(this)" class="text-[11px] text-emerald-400 light:text-emerald-600 hover:text-emerald-300 light:hover:text-emerald-700 font-medium px-2 py-0.5 rounded bg-emerald-400/10 light:bg-emerald-600/10 hover:bg-emerald-400/20 light:hover:bg-emerald-600/20 transition-colors flex items-center gap-1">
                        ✨ Auto-fill
                    </button>
                </div>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50">
                @error('name')
                    <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block mb-2 text-sm font-medium text-text-secondary">URL Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50 font-mono text-sm">
                @error('slug')
                    <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category -->
            <div>
                <label for="parent_id" class="block mb-2 text-sm font-medium text-text-secondary">Parent Category (Optional)</label>
                <div class="relative">
                    <select name="parent_id" id="parent_id" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all appearance-none cursor-pointer">
                        <option value="">-- None (Top Level) --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('parent_id')
                    <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color Code & Sort Order Row -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="color_code" class="block mb-2 text-sm font-medium text-text-secondary">Color Code (Hex)</label>
                    <div class="flex">
                        <input type="color" id="color_picker" class="h-10.5 w-12 p-0.5 border-0 bg-transparent rounded cursor-pointer mr-3" value="{{ old('color_code', $category->color_code) }}">
                        <input type="text" name="color_code" id="color_code" value="{{ old('color_code', $category->color_code) }}" required class="flex-1 px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-mono uppercase">
                    </div>
                    @error('color_code')
                        <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sort_order" class="block mb-2 text-sm font-medium text-text-secondary">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all">
                    @error('sort_order')
                        <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-text-secondary">Description (Optional)</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-3 bg-navy-900 border border-navy-700 rounded-lg text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all placeholder-text-muted/50 text-sm">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-alert-red">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
        <a href="{{ route('admin.categories.index') }}" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-text-muted hover:text-text-primary transition-colors text-center">Cancel</a>
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-200 rounded-lg bg-accent-blue hover:bg-accent-blue-hover focus:ring-2 focus:ring-offset-2 focus:ring-accent-blue outline-none shadow-lg shadow-accent-blue/20">
            Save Changes
        </button>
    </div>
</form>

<script>
    // Sync color picker with text input
    const colorPicker = document.getElementById('color_picker');
    const colorInput = document.getElementById('color_code');
    
    colorPicker.addEventListener('input', (e) => {
        colorInput.value = e.target.value.toUpperCase();
    });
    
    colorInput.addEventListener('input', (e) => {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            colorPicker.value = e.target.value;
        }
    });

    async function autoFillCategory(btn) {
        const nameInput = document.getElementById('name');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert('Please enter a category name first.');
            nameInput.focus();
            return;
        }

        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
        btn.disabled = true;

        try {
            const response = await fetch('{{ route("admin.categories.auto-fill") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name })
            });

            const data = await response.json();

            if (response.ok) {
                if (data.slug) document.getElementById('slug').value = data.slug;
                if (data.description) document.getElementById('description').value = data.description;
                if (data.color_code) {
                    document.getElementById('color_code').value = data.color_code;
                    document.getElementById('color_picker').value = data.color_code;
                }
            } else {
                alert(data.error || 'An error occurred while generating suggestions.');
            }
        } catch (error) {
            console.error(error);
            alert('A network error occurred. Please try again.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endsection
