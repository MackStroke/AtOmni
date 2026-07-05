@props([
    'column' => '', // The database column to sort by
    'label' => '',  // The display text
])

@php
    $currentSort = request('sort');
    $currentDir = request('dir', 'desc');
    
    // Determine the next direction if we click this header
    $nextDir = 'asc';
    if ($currentSort === $column) {
        $nextDir = $currentDir === 'asc' ? 'desc' : 'asc';
    }

    $isSorted = $currentSort === $column;
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir]) }}" class="group inline-flex items-center gap-1.5 hover:text-white light:hover:text-slate-900 transition-colors">
    {{ $label }}
    
    <span class="flex flex-col items-center justify-center -space-y-1">
        <svg class="w-3 h-3 {{ $isSorted && $currentDir === 'asc' ? 'text-electric' : 'text-text-muted/50 group-hover:text-text-muted light:group-hover:text-slate-500' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 8h16z"/></svg>
        <svg class="w-3 h-3 {{ $isSorted && $currentDir === 'desc' ? 'text-electric' : 'text-text-muted/50 group-hover:text-text-muted light:group-hover:text-slate-500' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8H4z"/></svg>
    </span>
</a>
