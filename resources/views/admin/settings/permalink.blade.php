@extends('admin.layouts.app')
@section('title', 'Permalink Settings')
@section('page-title', 'Permalink Settings')
@section('content')

<form method="POST" action="{{ route('admin.settings.permalink') }}" class="max-w-2xl">
    @csrf @method('PUT')

    <div class="glass-card rounded-xl p-6 space-y-5">
        <div>
            <h2 class="font-heading font-semibold text-text-primary">Permalink Structure</h2>
            <p class="text-sm text-text-muted mt-0.5">Choose the URL format for your article pages.</p>
        </div>

        <div class="space-y-3">
            @foreach([
                ['plain', 'Plain', 'https://atomni.in/?p=123'],
                ['day-name', 'Day and name', 'https://atomni.in/2026/03/14/sample-post/'],
                ['month-name', 'Month and name', 'https://atomni.in/2026/03/sample-post/'],
                ['numeric', 'Numeric', 'https://atomni.in/archives/123'],
                ['post-name', 'Post name', 'https://atomni.in/sample-post/'],
            ] as [$value, $label, $example])
                <label class="flex items-start gap-3 p-4 rounded-lg cursor-pointer transition-colors {{ $current === $value ? 'bg-electric/10 border border-electric/30' : 'bg-navy-800/40 border border-transparent hover:bg-navy-800/60' }}">
                    <input type="radio" name="permalink_structure" value="{{ $value }}" {{ $current === $value ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 text-electric border-navy-700 bg-navy-800 focus:ring-electric/30">
                    <div>
                        <span class="text-sm font-medium text-text-primary">{{ $label }}</span>
                        <code class="block text-xs text-text-muted mt-0.5 font-mono">{{ $example }}</code>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                Save Permalink Structure
            </button>
        </div>
    </div>
</form>

@endsection
