{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

    {{-- ─── Home page ──────────────────────────────────────────── --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- ─── Core static pages ──────────────────────────────────── --}}
    @foreach([
        ['url' => 'about',      'freq' => 'monthly',  'pri' => '0.7'],
        ['url' => 'contact',    'freq' => 'monthly',  'pri' => '0.5'],
        ['url' => 'careers',    'freq' => 'weekly',   'pri' => '0.6'],
        ['url' => 'advertise',  'freq' => 'monthly',  'pri' => '0.5'],
        ['url' => 'press-kit',  'freq' => 'monthly',  'pri' => '0.4'],
        ['url' => 'donate',     'freq' => 'monthly',  'pri' => '0.5'],
        ['url' => 'use-cases/client-intake-automation', 'freq' => 'weekly', 'pri' => '0.8'],
        ['url' => 'use-cases/document-processing-automation', 'freq' => 'weekly', 'pri' => '0.8'],
        ['url' => 'compare/atomni-vs-zapier', 'freq' => 'weekly', 'pri' => '0.8'],
    ] as $page)
    <url>
        <loc>{{ url($page['url']) }}</loc>
        <changefreq>{{ $page['freq'] }}</changefreq>
        <priority>{{ $page['pri'] }}</priority>
    </url>
    @endforeach

    {{-- ─── Legal pages (DB-driven) ───────────────────────────── --}}
    @foreach($pages as $page)
    <url>
        <loc>{{ url($page->slug) }}</loc>
        <lastmod>{{ ($page?->updated_at ?? now())->toAtomString() }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    @endforeach

    {{-- ─── Categories ─────────────────────────────────────────── --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('category/' . $category->slug) }}</loc>
        <lastmod>{{ ($category?->updated_at ?? now())->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach


    {{-- ─── Job postings ────────────────────────────────────────── --}}
    @foreach($jobs as $job)
    <url>
        <loc>{{ route('careers.show', $job->slug) }}</loc>
        <lastmod>{{ ($job?->updated_at ?? now())->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- ─── Articles ────────────────────────────────────────────── --}}
    @php $siteName = \App\Models\Setting::get('site_name', 'Atomni'); @endphp
    @foreach($posts as $post)
    <url>
        <loc>{{ route('frontend.article', $post->slug) }}</loc>
        <lastmod>{{ ($post?->updated_at ?? $post?->published_at ?? now())->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
        <news:news>
            <news:publication>
                <news:name>{{ $siteName }}</news:name>
                <news:language>en</news:language>
            </news:publication>
            <news:publication_date>{{ ($post?->published_at ?? $post?->updated_at ?? now())->toAtomString() }}</news:publication_date>
            <news:title>{{ $post->title }}</news:title>
        </news:news>
    </url>
    @endforeach

</urlset>
