@extends('admin.layouts.app')
@section('title', 'Site Health')
@section('page-title', 'Site Health')
@section('content')

<div class="max-w-4xl space-y-6">
    {{-- Server Info --}}
    <div class="glass-card rounded-xl p-6">
        <h2 class="font-heading font-semibold text-text-primary mb-4">Server Environment</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['PHP Version', $health['php_version'], 'bg-blue-500/15 text-blue-400'],
                ['Laravel Version', $health['laravel_version'], 'bg-purple-500/15 text-purple-400'],
                ['Server', $health['server_software'], 'bg-green-500/15 text-green-400'],
                ['Upload Limit', $health['max_upload_size'], 'bg-amber-500/15 text-amber-400'],
                ['POST Limit', $health['max_post_size'], 'bg-amber-500/15 text-amber-400'],
                ['Memory Limit', $health['memory_limit'], 'bg-red-500/15 text-red-400'],
                ['Max Execution', $health['max_execution_time'], 'bg-red-500/15 text-red-400'],
                ['DB Driver', $health['db_driver'], 'bg-cyan-500/15 text-cyan-400'],
                ['DB Size', $health['db_size'], 'bg-cyan-500/15 text-cyan-400'],
                ['Cache Driver', $health['cache_driver'], 'bg-emerald-500/15 text-emerald-400'],
                ['Session Driver', $health['session_driver'], 'bg-emerald-500/15 text-emerald-400'],
                ['Disk Free', $health['disk_free'], 'bg-orange-500/15 text-orange-400'],
            ] as [$label, $value, $style])
                <div class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/40">
                    <div class="w-2 h-2 rounded-full {{ explode(' ', $style)[1] }}"></div>
                    <div>
                        <span class="text-xs text-text-muted block">{{ $label }}</span>
                        <span class="text-sm font-medium text-text-primary">{{ $value }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PHP Extensions --}}
    <div class="glass-card rounded-xl p-6">
        <h2 class="font-heading font-semibold text-text-primary mb-4">PHP Extensions</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($health['extensions'] as $ext => $loaded)
                <div class="flex items-center gap-2 p-2 rounded-lg {{ $loaded ? 'bg-green-500/10' : 'bg-red-500/10' }}">
                    @if($loaded)
                        <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                    <span class="text-sm {{ $loaded ? 'text-green-300' : 'text-red-300' }}">{{ $ext }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
