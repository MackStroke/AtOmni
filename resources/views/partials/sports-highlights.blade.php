@php
    $featured = $highlights[0] ?? null;
    $listItems = array_slice($highlights, 1, 5);
@endphp

@if($featured)
<div class="flex items-center gap-3 mb-5">
    <div class="w-1 h-7 bg-electric rounded-full"></div>
    <h2 class="font-heading font-bold text-2xl text-text-primary">Sports Highlights</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Featured --}}
    <a href="{{ $featured['link'] }}" target="_blank" rel="noopener" class="block group relative rounded-xl overflow-hidden aspect-[16/10] bg-navy-800 lg:col-span-1">
        @if($featured['image'])
            <img loading="lazy" src="{{ $featured['image'] }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-blue-900 to-indigo-900 flex items-center justify-center">
                <span class="text-4xl">🏏</span>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <span class="text-[10px] font-bold uppercase tracking-widest text-electric mb-1 block">Featured</span>
            <h3 class="font-heading font-bold text-sm md:text-base text-white leading-snug group-hover:text-electric-light transition-colors line-clamp-2">
                {{ $featured['title'] }}
            </h3>
        </div>
    </a>

    {{-- Headlines list --}}
    <div class="lg:col-span-2">
        <div class="space-y-0">
            @foreach($listItems as $item)
            <a href="{{ $item['link'] }}" target="_blank" rel="noopener" class="flex items-start gap-3 py-3 border-b border-border-subtle last:border-0 group">
                <svg class="w-3 h-3 text-electric shrink-0 mt-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                <h4 class="text-sm font-medium text-text-secondary leading-snug group-hover:text-text-primary transition-colors line-clamp-1">
                    {{ $item['title'] }}
                </h4>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
