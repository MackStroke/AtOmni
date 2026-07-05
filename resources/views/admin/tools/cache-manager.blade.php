@extends('admin.layouts.app')
@section('title', 'Cache Manager')
@section('page-title', 'Cache Manager')
@section('content')

<div class="max-w-3xl space-y-6">
    <div class="glass-card rounded-xl p-6">
        <h2 class="font-heading font-semibold text-text-primary mb-2">Cache Management</h2>
        <p class="text-sm text-text-secondary mb-6">Clear various application caches to ensure your changes take effect.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['type' => 'views', 'label' => 'View Cache', 'desc' => 'Compiled Blade templates', 'color' => 'blue'],
                ['type' => 'cache', 'label' => 'App Cache', 'desc' => 'Application data cache', 'color' => 'purple'],
                ['type' => 'config', 'label' => 'Config Cache', 'desc' => 'Configuration files', 'color' => 'amber'],
                ['type' => 'routes', 'label' => 'Route Cache', 'desc' => 'Compiled route definitions', 'color' => 'green'],
            ] as $item)
                <form action="{{ route('admin.tools.cache.clear') }}" method="POST" class="p-4 rounded-lg bg-navy-800/40 flex items-center justify-between">
                    @csrf
                    <input type="hidden" name="type" value="{{ $item['type'] }}">
                    <div>
                        <h3 class="text-sm font-medium text-text-primary">{{ $item['label'] }}</h3>
                        <p class="text-xs text-text-muted">{{ $item['desc'] }}</p>
                    </div>
                    <button type="submit" class="shrink-0 px-3 py-1.5 rounded-lg bg-{{ $item['color'] }}-500/15 text-{{ $item['color'] }}-400 text-xs font-semibold hover:bg-{{ $item['color'] }}-500/25 transition-colors">
                        Clear
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-navy-700/50">
            <form action="{{ route('admin.tools.cache.clear') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-alert-red/15 text-alert-red hover:bg-alert-red/25 text-sm font-semibold transition-colors">
                    🗑️ Clear All Caches
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
