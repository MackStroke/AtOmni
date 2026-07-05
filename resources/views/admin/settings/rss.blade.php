@extends('admin.layouts.app')
@section('title', 'RSS Feed Settings')
@section('page-title', 'RSS Feed Settings')
@section('content')

{{-- ─── Light-mode overrides for the Import panel ───────────────────────────
     Root cause: html.light { color: #334155 } is unlayered CSS — it out-ranks
     Tailwind @layer utilities, so text-white on child elements loses the cascade.
     Fix: use explicit high-specificity selectors outside any layer.
──────────────────────────────────────────────────────────────────────────── --}}
<style>


/* ── Import panel card — solid background in light mode ─────────────────── */
html.light #rss-import-panel {
    background: linear-gradient(to right, #f5f3ff, #ffffff) !important;
    border-color: rgba(147, 51, 234, 0.35) !important;
}

/* ── Badge pill in light mode ────────────────────────────────────────────── */
html.light #last-import-badge span {
    background-color: rgba(147, 51, 234, 0.08);
    border-color: rgba(147, 51, 234, 0.25);
    color: #4c1d95;
}
</style>

@php
    $lastImportedAt    = \App\Models\Setting::get('rss_last_imported_at');
    $lastImportedCount = \App\Models\Setting::get('rss_last_imported_count', '0');
@endphp

<form method="POST" action="{{ route('admin.settings.rss') }}" class="max-w-2xl">
    @csrf @method('PUT')

    <div class="glass-card rounded-xl p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-semibold text-text-primary">RSS Feed Configuration</h2>
                <p class="text-sm text-text-muted mt-0.5">Configure your site's RSS feed output.</p>
            </div>
            <div class="flex items-center gap-2">
                <label for="rss_enabled" class="text-sm text-text-secondary">Enabled</label>
                <input type="checkbox" id="rss_enabled" name="rss_enabled" value="1"
                       {{ $rssFeeds['rss_enabled'] === 'true' ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-navy-700 bg-navy-800 text-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-800">
            </div>
        </div>

        {{-- Feed URL badge --}}
        <div class="flex items-center gap-3 p-3 rounded-lg bg-navy-800/40 border border-navy-700/30">
            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3.75 3a.75.75 0 000 1.5A10.75 10.75 0 0114.5 15.25a.75.75 0 001.5 0A12.25 12.25 0 003.75 3zM3.75 7.5a.75.75 0 000 1.5A6.25 6.25 0 0110 15.25a.75.75 0 001.5 0 7.75 7.75 0 00-7.75-7.75zM5.5 13.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
            </svg>
            <div class="text-sm min-w-0">
                <span class="text-text-secondary">Your feed URL: </span>
                <a href="{{ route('feed') }}" target="_blank" class="text-electric hover:underline font-mono text-xs break-all">{{ route('feed') }}</a>
            </div>
            <a href="{{ route('feed') }}" target="_blank" class="ml-auto shrink-0 text-xs px-2 py-1 rounded bg-electric/10 text-electric hover:bg-electric/20 transition-colors">Preview ↗</a>
        </div>

        <div>
            <label for="rss_title" class="block text-sm font-medium text-text-secondary mb-1.5">Feed Title</label>
            <input type="text" id="rss_title" name="rss_title" value="{{ old('rss_title', $rssFeeds['rss_title']) }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors">
            @error('rss_title') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="rss_description" class="block text-sm font-medium text-text-secondary mb-1.5">Feed Description</label>
            <textarea id="rss_description" name="rss_description" rows="2" required
                      class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors resize-none">{{ old('rss_description', $rssFeeds['rss_description']) }}</textarea>
            @error('rss_description') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="rss_max_items" class="block text-sm font-medium text-text-secondary mb-1.5">Maximum Items</label>
            <input type="number" id="rss_max_items" name="rss_max_items" value="{{ old('rss_max_items', $rssFeeds['rss_max_items']) }}" min="5" max="100" required
                   class="w-32 px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors">
            <p class="text-xs text-text-muted mt-1">Number of posts included in the RSS feed (5–100).</p>
            @error('rss_max_items') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="rss_custom_urls" class="block text-sm font-medium text-text-secondary mb-1.5">External RSS Feeds to Import</label>
            <textarea id="rss_custom_urls" name="rss_custom_urls" rows="4"
                      class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 transition-colors font-mono text-sm"
                      placeholder="https://techcrunch.com/feed/&#10;https://www.theverge.com/rss/index.xml&#10;https://news.ycombinator.com/rss">{{ old('rss_custom_urls', $rssFeeds['rss_custom_urls']) }}</textarea>
            <p class="text-xs text-text-muted mt-1">One URL per line. These feeds will be fetched and imported as <strong>published posts</strong> and will be visible on your website automatically.</p>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 shadow-lg shadow-electric/20">
            Save Settings
        </button>
        <span class="text-sm text-text-muted">Changes take effect immediately.</span>
    </div>
</form>

{{-- Import Now Panel --}}
<div id="rss-import-panel" class="glass-card rounded-xl p-6 mt-8 max-w-2xl"
     style="border: 1px solid color-mix(in srgb, var(--color-electric) 25%, transparent);
            background: linear-gradient(to right, color-mix(in srgb, var(--color-electric) 8%, transparent), color-mix(in srgb, var(--color-electric) 2%, transparent));">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="font-heading font-semibold text-text-primary flex items-center gap-2">
                <svg class="w-5 h-5 text-electric" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import External Feeds Now
            </h3>
            <p class="text-sm text-text-muted mt-1">
                Fetches all configured external RSS URLs and creates <strong>published posts</strong> automatically.
                Runs automatically every day at 02:00. Duplicate articles are skipped automatically.
            </p>
            @if($lastImportedAt)
            <p id="last-import-badge" class="text-xs text-text-muted mt-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-navy-700/50 border border-navy-600/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                    Last run: {{ \Carbon\Carbon::parse($lastImportedAt)->format('d M Y, H:i') }}
                    &mdash; {{ $lastImportedCount }} post(s) imported
                </span>
            </p>
            @else
            <p id="last-import-badge" class="text-xs text-text-muted mt-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-navy-700/50 border border-navy-600/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                    Never imported yet
                </span>
            </p>
            @endif
        </div>
        <div class="shrink-0">
            <button id="rss-import-btn"
                    type="button"
                    onclick="runRssImport()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-text-secondary hover:text-text-primary hover:bg-navy-800/60 transition-colors focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-950 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg id="import-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span id="import-btn-text">Import Now</span>
            </button>
        </div>
    </div>
    <div id="import-result" class="hidden mt-4 p-3 rounded-lg text-sm font-medium"></div>
    <p class="text-xs text-text-muted mt-4">
        ➡ After importing, review new posts in
        <a href="{{ route('admin.posts.index') }}" class="text-electric hover:underline">
            Admin → Posts
        </a>
    </p>
</div>

{{-- RSS Best Practices Guide --}}
<div class="glass-card rounded-xl border-l-4 border-electric p-6 mt-8 max-w-2xl bg-gradient-to-r from-navy-800 to-navy-900/50">
    <h3 class="font-heading font-semibold text-text-primary mb-4 flex items-center">
        <svg class="w-5 h-5 text-electric mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        Guide: Top Ranking RSS Feeds &amp; Best Practices
    </h3>
    <div class="space-y-4 text-sm text-text-secondary leading-relaxed">
        <p>Adding external RSS feeds can vastly enrich your site's content ecosystem. To get the best results for your audience, follow these best practices:</p>
        
        <ul class="list-disc pl-5 space-y-2">
            <li><strong class="text-white">Curate Niche Sources:</strong> Instead of broad news hubs (like Reuters or AP), find specialized feeds directly related to your primary taxonomy (e.g., specific Tech blogs, niche Finance analysts).</li>
            <li><strong class="text-white">Validate Before Adding:</strong> Ensure the feed's XML is valid. You can test feeds by pasting them in your browser. Look for standard <code>&lt;rss version="2.0"&gt;</code> or <code>&lt;feed xmlns="http://www.w3.org/2005/Atom"&gt;</code> tags.</li>
            <li><strong class="text-white">All imports are published:</strong> Newly imported posts will go live on your website automatically without needing editorial review.</li>
        </ul>

        <div class="mt-4 p-4 bg-navy-950/50 rounded-lg border border-navy-700">
            <h4 class="font-medium inline-block text-white mb-2 pb-1 border-b border-navy-600">Example high-quality feeds:</h4>
            <ul class="space-y-1 text-text-muted font-mono text-xs break-all">
                <li>https://techcrunch.com/feed/</li>
                <li>https://www.theverge.com/rss/index.xml</li>
                <li>https://feeds.feedburner.com/ndtvnews-top-stories</li>
                <li>https://timesofindia.indiatimes.com/rssfeedstopstories.cms</li>
            </ul>
        </div>
    </div>
</div>

{{-- Feed Preview --}}
<div class="glass-card rounded-xl p-6 mt-8 max-w-2xl">
    <h3 class="font-heading font-semibold text-text-primary mb-3">Your Feed Preview (XML structure)</h3>
    <div class="p-4 rounded-lg bg-navy-800/30 border border-navy-700/20 font-mono text-xs text-text-secondary leading-relaxed overflow-x-auto">
        <p class="text-text-muted">&lt;?xml version="1.0" encoding="UTF-8"?&gt;</p>
        <p>&lt;rss version="2.0"&gt;</p>
        <p class="ml-4">&lt;channel&gt;</p>
        <p class="ml-8">&lt;title&gt;<span class="text-electric">{{ $rssFeeds['rss_title'] }}</span>&lt;/title&gt;</p>
        <p class="ml-8">&lt;description&gt;<span class="text-electric">{{ \Illuminate\Support\Str::limit($rssFeeds['rss_description'], 50) }}</span>&lt;/description&gt;</p>
        <p class="ml-8">&lt;link&gt;{{ url('/') }}&lt;/link&gt;</p>
        <p class="ml-8 text-text-muted">&lt;!-- {{ $rssFeeds['rss_max_items'] }} most recent items --&gt;</p>
        <p class="ml-4">&lt;/channel&gt;</p>
        <p>&lt;/rss&gt;</p>
    </div>
</div>

@section('scripts')
<script>
async function runRssImport() {
    const btn      = document.getElementById('rss-import-btn');
    const btnText  = document.getElementById('import-btn-text');
    const icon     = document.getElementById('import-icon');
    const result   = document.getElementById('import-result');

    btn.disabled = true;
    btnText.textContent = 'Importing…';
    icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>`;
    result.className = 'hidden mt-4 p-3 rounded-lg text-sm font-medium';

    try {
        const res  = await fetch('{{ route('admin.tools.rss-import') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        result.classList.remove('hidden');
        if (data.success) {
            result.classList.add('bg-green-900/30', 'border', 'border-green-500/30', 'text-green-300');
            result.textContent = '✓ ' + data.message;
            if (data.last_imported) {
                document.getElementById('last-import-badge').innerHTML =
                    `<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-navy-700/50 border border-navy-600/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        Last run: ${data.last_imported} &mdash; ${data.imported} post(s) imported
                     </span>`;
            }
        } else {
            result.classList.add('bg-red-900/30', 'border', 'border-red-500/30', 'text-red-300');
            result.textContent = '✗ ' + data.message;
        }
    } catch (err) {
        result.classList.remove('hidden');
        result.classList.add('bg-red-900/30', 'border', 'border-red-500/30', 'text-red-300');
        result.textContent = '✗ Network error: ' + err.message;
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Import Now';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>`;
    }
}
</script>
@endsection

@endsection
