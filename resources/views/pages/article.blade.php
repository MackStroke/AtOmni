@extends('layouts.app')

@section('title', preg_replace('/\s+?(\S+)?$/', '', mb_substr($post->title, 0, 61)) . ' | ' . \App\Models\Setting::get('site_name', 'Atomni'))
@section('meta-description', \Illuminate\Support\Str::limit($post?->clean_excerpt ?? '', 155))
@php
    $isValidRedirect = !empty($post->redirect_url) && filter_var($post->redirect_url, FILTER_VALIDATE_URL);
    $isThinSyndication = $post->is_rss && $isValidRedirect && str_word_count(strip_tags($post->content)) < 300;
    $canonicalUrl = $isThinSyndication ? $post->redirect_url : route('frontend.article', $post->slug);
@endphp
@section('canonical', $canonicalUrl)
@section('og-type', 'article')
@section('og-image', $post->featuredImageUrl())
@section('og-image-alt', e($post->title))

{{-- Article-specific Open Graph & News meta --}}
@section('head-extra')
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at?->toIso8601String() }}">
@if($post->author)
<meta property="article:author" content="{{ $post->author->name }}">
@endif
@if($post->category)
<meta property="article:section" content="{{ $post->category->name }}">
@endif
@if($post->tags->isNotEmpty())
<meta name="news_keywords" content="{{ $post->tags->pluck('name')->implode(', ') }}">
@foreach($post->tags as $tag)
<meta property="article:tag" content="{{ $tag->name }}">
@endforeach
@endif
@endsection

@section('schema')
@php
    $siteName    = \App\Models\Setting::get('site_name', 'Atomni');
    $siteUrl     = url('/');
    $articleUrl  = route('frontend.article', $post->slug);
    $imageUrl    = $post->featuredImageUrl();
    $authorName  = $post->author?->name ?? 'Atomni Writer';
    $wordCount   = str_word_count(strip_tags($post->content));

    // Resolve logo: prefer site_logo_dark setting, fall back to favicon
    $logoSetting = \App\Models\Setting::get('site_logo_dark', '');
    $logoUrl     = $logoSetting
        ? asset('storage/' . $logoSetting)
        : $siteUrl . '/favicon.ico';

    // Author profile URL — link to team member page if one is linked
    $authorProfileUrl = null;
    if ($post->author?->teamMember) {
        // Link to the about page with a hash anchor to their profile section
        $authorProfileUrl = url('/about') . '#team-' . $post->author->teamMember->id;
    }
@endphp
@php
    $newsSchema = [
        "@context" => "https://schema.org",
        "@type" => "NewsArticle",
        "@id" => $articleUrl,
        "headline" => $post->title,
        "description" => $post?->clean_excerpt ?? '',
        "image" => [
            "@type" => "ImageObject",
            "url" => $imageUrl,
            "width" => 1200,
            "height" => 630
        ],
        "thumbnailUrl" => $imageUrl,
        "url" => $articleUrl,
        "inLanguage" => "en",
        "datePublished" => $post->published_at?->toAtomString(),
        "dateModified" => $post->updated_at?->toAtomString(),
        "author" => [
            "@type" => "Person",
            "name" => $authorName,
        ],
        "publisher" => [
            "@type" => "NewsMediaOrganization",
            "name" => $siteName,
            "url" => $siteUrl,
            "logo" => [
                "@type" => "ImageObject",
                "url" => $logoUrl,
                "width" => 600,
                "height" => 60
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $articleUrl
        ],
        "articleSection" => $post->category?->name ?? 'News',
        "keywords" => $post->tags->pluck('name')->implode(', '),
        "wordCount" => $wordCount,
        "commentCount" => $comments->count(),
        "isPartOf" => [
            "@type" => "WebSite",
            "name" => $siteName,
            "url" => $siteUrl
        ]
    ];
    if ($authorProfileUrl) {
        $newsSchema["author"]["url"] = $authorProfileUrl;
        $newsSchema["author"]["sameAs"] = [$authorProfileUrl];
    }
@endphp
<script type="application/ld+json">{!! json_encode($newsSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ url('/') }}"
        }
        @if($post->category),
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ e($post->category->name) }}",
            "item": "{{ url('category/' . $post->category->slug) }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ e($post->title) }}",
            "item": "{{ $articleUrl }}"
        }
        @else,
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ e($post->title) }}",
            "item": "{{ $articleUrl }}"
        }
        @endif
    ]
}
</script>
@php
    // Use explicit FAQs if available
    $faqItems = $post?->faqs ?? [];
    
    // Fallback: Auto-extract FAQ items from post content
    if (empty($faqItems) && $post->content) {
        $dom = new DOMDocument();
        @$dom->loadHTML('<meta charset="utf-8">' . $post->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);
        $questionWords = '/^(what|who|when|where|why|how|is|are|can|does|did|will|has|have|should|which|was|were)/i';
        foreach ($xpath->query('//h2|//h3') as $heading) {
            $text = trim($heading->textContent);
            if (preg_match($questionWords, $text)) {
                $next = $heading->nextSibling;
                while ($next && $next->nodeType === XML_TEXT_NODE) {
                    $next = $next->nextSibling;
                }
                if ($next && strtolower($next->nodeName) === 'p') {
                    $faqItems[] = [
                        'question' => $text,
                        'answer' => trim(strip_tags($next->textContent)),
                    ];
                    if (count($faqItems) >= 5) break;
                }
            }
        }
    }
@endphp
@if(count($faqItems) > 0)
@php
    $faqSchema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => collect($faqItems)->map(function($faq) {
            return [
                "@type" => "Question",
                "name" => $faq['question'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $faq['answer']
                ]
            ];
        })->toArray()
    ];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endsection

@section('content')

{{-- Article Hero --}}
<article itemscope itemtype="https://schema.org/NewsArticle">
    @if($post->tldr)
    <div class="answer-nugget max-w-4xl mx-auto mt-4 px-6 sm:hidden" itemprop="abstract">
        <div class="bg-electric/10 border-l-4 border-electric p-4 rounded-r-xl">
            <strong class="text-electric block mb-1">In short:</strong>
            <p class="text-text-primary text-sm">{{ $post->tldr }}</p>
        </div>
    </div>
    @endif
    @php
        $authorImage = $post->author?->profile_image ?? $post->author?->teamMember?->photo_path;
        $authorName = $post->author?->name === 'Admin' ? 'Atomni Editorial Desk' : ($post->author?->name ?? 'Atomni Writer');
    @endphp
    <header class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4 flex-wrap">
            <a href="{{ url('/') }}" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($post->category)
            <a href="{{ url('category/' . $post->category->slug) }}" class="hover:text-electric transition-colors">{{ $post->category->name }}</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-text-secondary">Article</span>
        </nav>
        
        <div class="flex items-center gap-2 mb-4">
            @if($post->category)
            <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-electric text-white">{{ $post->category->name }}</span>
            @endif
            @if($post->is_featured)
            <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-amber text-navy-950">Featured</span>
            @endif
        </div>
        
        <h1 class="font-heading font-bold text-3xl sm:text-4xl lg:text-5xl text-text-primary leading-tight mb-4">
            {{ $post->title }}
        </h1>
        
        @php
            $sourceDomain = 'External Source';
            $sourceUrl = $post->redirect_url;
            
            if (!empty($post->redirect_url)) {
                $sourceDomain = parse_url($post->redirect_url, PHP_URL_HOST);
            } elseif (preg_match('/^\[RSS:\s*([^\]]+)\]/i', $post->excerpt, $matches)) {
                $sourceDomain = $matches[1];
            } elseif (!empty($post->content) && preg_match('/href="(https?:\/\/(?!www\.atomni\.in|atomni\.in|localhost|127\.0\.0\.1|[\w\.-]+\.atomni\.in)[^"\/]+)/i', $post->content, $m)) {
                $sourceDomain = parse_url($m[1], PHP_URL_HOST);
                $sourceUrl = $m[1];
            }
            
            // Clean up publisher name
            $cleanPublisher = 'External Source';
            if ($sourceDomain && $sourceDomain !== 'External Source') {
                $domainLower = strtolower($sourceDomain);
                $domainLower = preg_replace('/^(www|feeds|feed|rss|news|assets)\./', '', $domainLower);
                
                $knownPublishers = [
                    'marktechpost.com' => 'MarkTechPost',
                    'pcgamer.com' => 'PC Gamer',
                    'techcrunch.com' => 'TechCrunch',
                    'theverge.com' => 'The Verge',
                    'arstechnica.com' => 'Ars Technica',
                    'wired.com' => 'Wired',
                    'engadget.com' => 'Engadget',
                    'gizmodo.com' => 'Gizmodo',
                    'nytimes.com' => 'New York Times',
                    'bloomberg.com' => 'Bloomberg',
                    'reuters.com' => 'Reuters',
                    'apnews.com' => 'Associated Press',
                    'bbc.com' => 'BBC News',
                    'bbc.co.uk' => 'BBC News',
                    'cnet.com' => 'CNET',
                    'venturebeat.com' => 'VentureBeat',
                    'forbes.com' => 'Forbes',
                ];
                
                $found = false;
                foreach ($knownPublishers as $key => $name) {
                    if (str_contains($domainLower, $key)) {
                        $cleanPublisher = $name;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $parts = explode('.', $domainLower);
                    $cleanPublisher = count($parts) > 1 ? ucfirst($parts[0]) : ucfirst($domainLower);
                }
            }
        @endphp

        @if($post->is_rss || $post->redirect_url || $sourceDomain !== 'External Source')
            <div class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-navy-800 border border-navy-700">
                <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <a href="{{ filter_var($sourceUrl, FILTER_VALIDATE_URL) ? $sourceUrl : '#' }}" target="_blank" rel="noopener nofollow" class="text-xs text-text-secondary font-medium hover:text-electric transition-colors">
                    Source: {{ $cleanPublisher }}
                </a>
            </div>
        @endif

        <p class="text-text-secondary text-lg leading-relaxed max-w-3xl mb-6">
            {{ $post->clean_excerpt }}
        </p>
        
        <address class="flex items-center gap-4 not-italic py-4 border-y border-navy-700/30" rel="author">
            <div class="w-12 h-12 rounded-full bg-navy-700 overflow-hidden flex items-center justify-center text-white font-bold text-lg">
                @if($authorImage)
                    <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($authorImage) }}" alt="{{ $authorName }}" class="w-full h-full object-cover" itemprop="image">
                @else
                    {{ substr($authorName, 0, 1) }}
                @endif
            </div>
            <div>
                <span class="text-text-primary font-medium text-base block" itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name">{{ $authorName }}</span>
                </span>
                <div class="text-text-muted text-sm flex items-center flex-wrap gap-2 mt-1">
                    <time datetime="{{ $post->published_at?->toIso8601String() }}" itemprop="datePublished">{{ $post->published_at?->format('F d, Y') ?? 'Date' }}</time>
                    <span>&middot;</span>
                    <span>{{ $post?->reading_time ?? 5 }} min read</span>
                    <span>&middot;</span>
                    <span>{{ number_format($post->views_count) }} views</span>
                </div>
            </div>
        </address>
    </header>

    <div class="relative w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="w-full rounded-2xl overflow-hidden shadow-2xl bg-navy-900" style="aspect-ratio: 21/9; min-height: 300px;">
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    </div>

    {{-- Article Body --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            {{-- Main Content (2/3) --}}
            <div class="lg:col-span-2">

                {{-- Share bar --}}
                <div class="flex items-center gap-3 mb-8 pb-6 border-b border-navy-700/30">
                    <span class="text-text-muted text-sm">Share:</span>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" class="w-9 h-9 rounded-lg bg-navy-800 hover:bg-electric flex items-center justify-center text-text-secondary hover:text-white transition-all" aria-label="X (Twitter)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" target="_blank" class="w-9 h-9 rounded-lg bg-navy-800 hover:bg-electric flex items-center justify-center text-text-secondary hover:text-white transition-all" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="w-9 h-9 rounded-lg bg-navy-800 hover:bg-[#1877F2] flex items-center justify-center text-text-secondary hover:text-white transition-all" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!');" class="w-9 h-9 rounded-lg bg-navy-800 hover:bg-electric flex items-center justify-center text-text-secondary hover:text-white transition-all" aria-label="Copy link" title="Copy Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </button>
                </div>

                {{-- ═══════════════════════════════════════════════════════
                     AEO: TL;DR / Quick Answer Box (Desktop)
                     Shown right after the share bar so AI answer engines
                     (Perplexity, SGE, ChatGPT Browse) see it in the first
                     ~100 words — the window they use to form featured snippets.
                     ═══════════════════════════════════════════════════════ --}}
                @if($post->tldr)
                <div class="mb-8 p-5 rounded-xl border-l-4 border-electric bg-electric/5 hidden sm:flex gap-4 shadow-sm" role="note" aria-label="Quick Summary" itemprop="abstract">
                    <div class="shrink-0 mt-0.5">
                        <div class="w-8 h-8 rounded-full bg-electric/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <div>
                        <strong class="text-text-primary font-heading mb-1 block">In short:</strong>
                        <p class="text-text-secondary text-[15px] leading-relaxed">{{ $post->tldr }}</p>
                    </div>
                </div>
                @elseif($post->clean_excerpt)
                <div class="mb-8 p-4 rounded-xl border-l-4 border-electric bg-electric/5 flex gap-3 shadow-sm" role="note" aria-label="Quick Summary">
                    <div class="shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-text-secondary text-sm leading-relaxed">{{ $post->clean_excerpt }}</p>
                    </div>
                </div>
                @endif

                {{-- Article content --}}
                <div class="prose-atomni max-w-prose mx-auto space-y-6 text-text-secondary leading-relaxed text-[15px]">
                    @php
                        $content = $post->content;
                        $ctaHtml = '<div class="my-8 p-8 bg-navy-900 rounded-xl border border-electric/30 text-center shadow-lg"><h3 class="font-heading font-bold text-2xl text-text-primary mb-3">Support Atomni</h3><p class="text-text-secondary mb-6 max-w-md mx-auto">Help us keep delivering high-quality, independent journalism. Your support makes a difference.</p><a href="'.route('donate').'" class="inline-block px-8 py-3 rounded-lg bg-electric text-white font-bold hover:bg-electric-light transition-colors shadow-md">Support Our Work</a></div>';
                        
                        $adInArticleRaw = \App\Models\Setting::get('ad_in_article', '');
                        $adInArticleHtml = $adInArticleRaw ? '<div class="my-8 flex justify-center w-full overflow-hidden">' . $adInArticleRaw . '</div>' : '';

                        if (!empty(trim($content))) {
                            $internalErrors = libxml_use_internal_errors(true);
                            $dom = new \DOMDocument();
                            // Safely convert UTF-8 to HTML-ENTITIES to guarantee proper parsing of messy fragments without warnings
                            $safeHtml = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
                            @$dom->loadHTML($safeHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                            
                            $paragraphs = $dom->getElementsByTagName('p');
                            $targetParagraph = null;
                            $secondTargetParagraph = null;

                            // Traverse and find early paragraphs that are not boilerplate
                            $paraCount = 0;
                            $validParas = [];
                            foreach ($paragraphs as $p) {
                                $paraCount++;
                                $text = trim($p->textContent);
                                $lowerText = strtolower($text);
                                
                                if (strlen($text) < 25 ||
                                    str_starts_with($text, '[RSS:') ||
                                    str_starts_with($lowerText, 'source:') ||
                                    str_starts_with($lowerText, 'via:') ||
                                    str_starts_with($lowerText, 'image:') ||
                                    str_contains($lowerText, 'originally published') ||
                                    str_contains($lowerText, 'follow us on') ||
                                    str_contains($lowerText, 'subscribe to') ||
                                    str_contains($lowerText, 'read more:')) {
                                    continue;
                                }
                                $validParas[] = $p;
                            }

                            // If we have valid paragraphs, pick the 1st for Ad, 4th for CTA
                            if (count($validParas) > 0) {
                                $targetParagraph = $validParas[0]; // After 1st valid paragraph
                            }
                            if (count($validParas) > 3) {
                                $secondTargetParagraph = $validParas[3]; // After 4th valid paragraph
                            } elseif (count($validParas) > 1) {
                                $secondTargetParagraph = $validParas[count($validParas) - 1]; // Or last valid if less than 4
                            }
                            
                            // Insert Ad
                            if ($targetParagraph && $adInArticleHtml) {
                                $adDom = new \DOMDocument();
                                $safeAd = mb_convert_encoding($adInArticleHtml, 'HTML-ENTITIES', 'UTF-8');
                                @$adDom->loadHTML($safeAd, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                                $adNode = $dom->importNode($adDom->documentElement, true);
                                
                                if ($targetParagraph->nextSibling) {
                                    $targetParagraph->parentNode->insertBefore($adNode, $targetParagraph->nextSibling);
                                } else {
                                    $targetParagraph->parentNode->appendChild($adNode);
                                }
                            }

                            // Insert CTA
                            if ($secondTargetParagraph) {
                                $ctaDom = new \DOMDocument();
                                $safeCta = mb_convert_encoding($ctaHtml, 'HTML-ENTITIES', 'UTF-8');
                                @$ctaDom->loadHTML($safeCta, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                                $ctaNode = $dom->importNode($ctaDom->documentElement, true);
                                
                                if ($secondTargetParagraph->nextSibling) {
                                    $secondTargetParagraph->parentNode->insertBefore($ctaNode, $secondTargetParagraph->nextSibling);
                                } else {
                                    $secondTargetParagraph->parentNode->appendChild($ctaNode);
                                }
                            }
                            
                            // Save HTML and safely strip any implicitly added wrapper tags
                            if ($targetParagraph || $secondTargetParagraph) {
                                $rawHtml = $dom->saveHTML();
                                // Strip DOCTYPE (case-insensitive, handles multi-line and PUBLIC variants)
                                $rawHtml = preg_replace('/<!DOCTYPE[^>]*>/si', '', $rawHtml);
                                // Strip html and body wrappers (including ones with attributes)
                                $rawHtml = preg_replace('/<\/?html[^>]*>/si', '', $rawHtml);
                                $rawHtml = preg_replace('/<\/?body[^>]*>/si', '', $rawHtml);
                                $content = trim($rawHtml);
                            }

                            libxml_clear_errors();
                            libxml_use_internal_errors($internalErrors);
                        }
                    @endphp
                    {!! $content !!}
                </div>

                {{-- Explicit FAQs rendering in content --}}
                @if(is_array($post->faqs) && count($post->faqs) > 0)
                <div class="mt-12 pt-8 border-t border-navy-700/30">
                    <h2 class="text-2xl font-bold font-heading text-text-primary mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        @foreach($post->faqs as $faq)
                        <div class="bg-navy-900/40 rounded-xl p-5 border border-navy-800/50">
                            <h3 class="text-lg font-bold text-text-primary mb-2">{{ $faq['question'] }}</h3>
                            <p class="text-text-secondary text-[15px]">{{ $faq['answer'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Tags --}}
                @if($post->tags->count() > 0)
                <div class="flex flex-wrap items-center gap-2 mt-10 pt-6 border-t border-navy-700/30">
                    <span class="text-text-muted text-sm mr-2">Tags:</span>
                    @foreach($post->tags as $tag)
                        <a href="{{ url('search?tag=' . urlencode($tag->name)) }}" class="px-3 py-1.5 rounded-full text-xs font-medium text-text-secondary bg-navy-800 hover:bg-electric hover:text-white transition-all">#{{ $tag->name }}</a>
                    @endforeach
                </div>
                @endif

                {{-- Author bio --}}
                <div class="glass-card rounded-xl p-6 mt-8 flex flex-col sm:flex-row gap-5">
                    <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-navy-800 flex items-center justify-center text-2xl font-bold text-white">
                        @if($authorImage)
                            <img loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($authorImage) }}" alt="{{ $authorName }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($authorName, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-text-primary mb-1">{{ $authorName }}</h3>
                        <p class="text-electric text-sm mb-2">Editorial Desk</p>
                    </div>
                </div>

                {{-- Comments Section --}}
                <section class="mt-12">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 rounded-full bg-electric"></div>
                        <h2 class="font-heading font-bold text-2xl text-text-primary">Comments ({{ $comments->count() }})</h2>
                    </div>

                    @if(session('success'))
                        <div class="bg-success/15 border border-success/30 text-success px-4 py-3 rounded-xl mb-6 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="glass-card rounded-xl p-6 mb-6">
                        <form action="{{ route('frontend.article.comment', $post->slug) }}" method="POST" id="comment-form">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <input type="text" name="guest_name" id="guest_name" aria-label="Your name" autocomplete="name" placeholder="Your name" value="{{ old('guest_name') }}" required class="w-full px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                                    @error('guest_name') <span class="text-alert-red text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input type="email" name="guest_email" id="guest_email" aria-label="Your email" autocomplete="email" placeholder="Your email" value="{{ old('guest_email') }}" required class="w-full px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                                    @error('guest_email') <span class="text-alert-red text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <textarea name="comment_text" id="comment_text" aria-label="Comment text" placeholder="Share your thoughts..." required class="w-full px-4 py-3 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors resize-none h-24">{{ old('comment_text') }}</textarea>
                                @error('comment_text') <span class="text-alert-red text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="px-6 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all">Post Comment</button>
                            </div>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @forelse($comments as $comment)
                            <div class="glass-card rounded-xl p-5">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 rounded-full bg-electric/20 flex items-center justify-center text-electric font-bold text-xs">
                                        {{ strtoupper(substr($comment->displayName(), 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="text-text-primary text-sm font-medium">{{ $comment->displayName() }}</span>
                                        @if($comment->user_id && $comment->user_id === $post->author_id)
                                            <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-electric/10 text-electric border border-electric/20">Author</span>
                                        @endif
                                        <time class="text-text-muted text-xs ml-2 cursor-help" title="{{ $comment->created_at->format('M d, Y h:i A') }}" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                                <p class="text-text-secondary text-sm leading-relaxed">{{ $comment->comment_text }}</p>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-text-muted text-sm">No comments yet. Be the first to share your thoughts!</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Sidebar (1/3) --}}
            <aside class="space-y-6">
                @php $ad_sidebar = \App\Models\Setting::get('ad_sidebar', ''); @endphp
                @if($ad_sidebar)
                <div class="glass-card rounded-xl p-4 flex justify-center items-center overflow-hidden">
                    {!! $ad_sidebar !!}
                </div>
                @endif

                <div class="glass-card rounded-xl p-6">
                    <h3 class="font-heading font-bold text-lg text-text-primary mb-4">Related Articles</h3>
                    <div class="space-y-4">
                        @if(is_countable($relatedPosts) && count($relatedPosts) > 0 || !is_countable($relatedPosts) && $relatedPosts)
                            @foreach($relatedPosts as $rel)
                            <a href="{{ route('frontend.article', $rel->slug) }}"
                               class="flex items-start gap-3 group rounded-lg p-1.5 -mx-1.5 hover:bg-navy-800/50 transition-colors">
                                {{-- Thumbnail --}}
                                <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-navy-800">
                                    <img loading="lazy"
                                         src="{{ $rel->featuredImageUrl() }}"
                                         alt="{{ $rel->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                {{-- Text --}}
                                <div class="flex-1 min-w-0">
                                    @if($rel->category)
                                    <span class="text-electric text-[10px] font-bold uppercase tracking-wider">{{ $rel->category->name }}</span>
                                    @endif
                                    <h4 class="text-text-primary text-sm font-medium leading-snug group-hover:text-electric transition-colors mt-0.5 line-clamp-3">{{ $rel->title }}</h4>
                                </div>
                            </a>
                            @if(!$loop->last)<div class="border-b border-navy-700/20 my-0.5"></div>@endif
                            @endforeach
                        @else
                            <p class="text-text-muted text-sm">No related articles found.</p>
                        @endif
                    </div>
                </div>


            </aside>
        </div>
    </div>
</article>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let activeTime = 0;
    let isActive = true;
    let lastPing = Date.now();
    
    // Check if user is active (not switched tabs)
    document.addEventListener('visibilitychange', () => {
        isActive = !document.hidden;
        if (isActive) {
            lastPing = Date.now();
        }
    });

    setInterval(() => {
        if (!isActive) return;
        
        let now = Date.now();
        // If it's been more than 15 seconds since last ping, maybe they slept or paused, don't count it fully
        if (now - lastPing > 15000) {
            lastPing = now;
            return;
        }
        
        // Send ping
        fetch('{{ route("api.analytics.ping") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ post_id: {{ $post->id }} }),
            keepalive: true // Ensure it sends even if page unloads
        }).catch(() => {});
        
        lastPing = now;
    }, 10000); // 10 seconds
});
</script>
@endpush

@endsection
