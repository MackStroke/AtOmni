{{-- ═══════════════════════════════════════════════════════════
     BREAKING NEWS TICKER — Shared Partial
     Include via: @include('partials.ticker')
     ═══════════════════════════════════════════════════════════ --}}
@php
    $tickerEnabled = \App\Models\Setting::get('ticker_enabled', '0') === '1';
    $tickerMode = \App\Models\Setting::get('ticker_mode', 'latest_posts');
    $tickerSpeed = \App\Models\Setting::get('ticker_speed', '25');
    
    $tickerItems = [];
    if ($tickerMode === 'latest_posts') {
        $posts = \App\Models\Post::published()
            ->latest('published_at')
            ->take(5)
            ->get(['title', 'slug']);
        foreach($posts as $post) {
            $tickerItems[] = [
                'text' => $post->title,
                'url' => route('frontend.article', $post->slug)
            ];
        }
    } else {
        $tickerTextRaw = \App\Models\Setting::get('ticker_text', '');
        $lines = array_filter(array_map('trim', explode("\n", $tickerTextRaw)));
        foreach($lines as $line) {
            $tickerItems[] = [
                'text' => $line,
                'url' => null
            ];
        }
    }
@endphp

@if($tickerEnabled && count($tickerItems) > 0)
<div class="fixed inset-x-0 bg-navy-900/95 backdrop-blur-md border-b border-navy-700/50 overflow-hidden shadow-sm light:bg-slate-100/95 light:border-slate-300/80" style="top:94px;z-index:45">
    <div class="max-w-7xl mx-auto px-4 flex items-center h-9">
        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-alert-red text-white badge-live mr-4">
            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
            {{ $tickerMode === 'latest_posts' ? 'Latest' : 'Announcement' }}
        </span>
        <div class="overflow-hidden whitespace-nowrap flex-1">
            <p class="ticker-animate text-sm text-text-secondary inline-block min-w-max pr-[100vw]" style="animation-duration: {{ $tickerSpeed }}s;">
                @foreach($tickerItems as $item)
                    <span class="mx-8">
                        @if($item['url'])
                            <a href="{{ $item['url'] }}" class="hover:text-electric transition-colors">{{ $item['text'] }}</a>
                        @else
                            {{ $item['text'] }}
                        @endif
                    </span>
                @endforeach
            </p>
        </div>
    </div>
</div>

{{-- Spacer for topbar(30) + header(64) + ticker(36) = 130px --}}
<div class="pointer-events-none" style="height:130px"></div>
@else
{{-- Spacer for topbar(30) + header(64) = 94px --}}
<div class="pointer-events-none" style="height:94px"></div>
@endif

