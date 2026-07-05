@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-2">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-2 rounded-lg bg-navy-800/50 text-text-muted text-sm cursor-default">← Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors">← Prev</a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-text-muted text-sm">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-4 py-2 rounded-lg bg-electric text-white text-sm font-semibold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-4 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors flex items-center gap-1">
            Next <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="px-3 py-2 rounded-lg bg-navy-800/50 text-text-muted text-sm cursor-default">Next →</span>
    @endif
</nav>
@endif
