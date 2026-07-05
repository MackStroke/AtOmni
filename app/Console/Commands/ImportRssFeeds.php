<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ImportRssFeeds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rss:import
                            {--dry-run : Preview what would be imported without saving anything}
                            {--limit=50 : Maximum items to import per feed}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch external RSS feeds and create published posts automatically';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $urlsRaw  = Setting::get('rss_custom_urls', '');
        $urls     = array_filter(array_map('trim', explode("\n", $urlsRaw)));
        $isDryRun = $this->option('dry-run');
        $limit    = (int) $this->option('limit');

        if (empty($urls)) {
            $this->warn('No external RSS feed URLs configured. Add them in Admin → Settings → RSS Feed.');
            return self::SUCCESS;
        }

        // Get the default author — first super_admin, fallback to any user
        $defaultAuthor = User::where('role', 'super_admin')->first()
            ?? User::first();

        if (!$defaultAuthor) {
            $this->error('No users found. Cannot assign author to imported posts.');
            return self::FAILURE;
        }

        $totalImported = 0;
        $totalSkipped  = 0;

        foreach ($urls as $url) {
            $this->line('');
            $this->info("📡 Fetching: {$url}");

            try {
                $items = $this->parseFeed($url);
            } catch (\Throwable $e) {
                $this->error("  ✗ Failed to fetch/parse feed: " . $e->getMessage());
                continue;
            }

            if (empty($items)) {
                $this->warn('  No items found in feed.');
                continue;
            }

            $imported = 0;
            $skipped  = 0;
            $domain   = parse_url($url, PHP_URL_HOST) ?? $url;

            foreach (array_slice($items, 0, $limit) as $item) {
                if (empty($item['title'])) {
                    $skipped++;
                    continue;
                }

                $baseSlug = Str::slug(Str::limit($item['title'], 80, ''));
                $slug     = $baseSlug;

                // Ensure unique slug (checking soft-deleted as well) to avoid constraint violation
                if (Post::withTrashed()->where('slug', $slug)->exists()) {
                    $skipped++;
                    $this->line("  » Skipped (already exists): {$item['title']}");
                    continue;
                }

                if ($isDryRun) {
                    $this->line("  [DRY-RUN] Would import: {$item['title']}");
                    $imported++;
                    continue;
                }

                // Auto-assign category from feed item category, or fall back
                $categoryId = $this->resolveCategory($item['category'] ?? null);

                $content = !empty($item['content'])
                    ? $item['content']
                    : ('<p>' . e($item['description'] ?? '') . '</p>');

                $excerpt = Str::limit(strip_tags($item['description'] ?? $item['title']), 250);

                $featuredImage = $item['image'] ?? null;
                if ($featuredImage && strlen($featuredImage) > 255) {
                    $featuredImage = null; // Prevent SQL truncation errors on long URLs
                }

                Post::create([
                    'title'          => $item['title'],
                    'slug'           => $slug,
                    'content'        => $content,
                    'excerpt'        => '[RSS: ' . $domain . '] ' . $excerpt,
                    'featured_image' => $featuredImage,
                    'status'         => 'published',
                    'author_id'      => $defaultAuthor->id,
                    'category_id'    => $categoryId,
                    'reading_time'   => Post::calculateReadingTime($content),
                    'published_at'   => now(),
                ]);

                $this->line("  ✓ Imported: {$item['title']}");
                $imported++;
            }

            $this->info("  Feed done — {$imported} imported, {$skipped} skipped.");
            $totalImported += $imported;
            $totalSkipped  += $skipped;
        }

        // Record last import time and count
        if (!$isDryRun) {
            Setting::set('rss_last_imported_at', now()->toDateTimeString());
            Setting::set('rss_last_imported_count', (string) $totalImported);
            // Bust the RSS output cache so new aggregated content is reflected
            Cache::store('file')->forget('rss_feed_xml');
        }

        $this->line('');
        $this->info("✅ RSS import complete — {$totalImported} posts imported, {$totalSkipped} skipped.");

        return self::SUCCESS;
    }

    /**
     * Fetch and parse a feed URL. Supports RSS 2.0 and Atom.
     * Returns array of ['title', 'link', 'description', 'content', 'image', 'category', 'pubDate'].
     */
    private function parseFeed(string $url): array
    {
        // Set a user-agent and timeout so servers don't block us or hang
        $context = stream_context_create([
            'http' => [
                'timeout'          => 15,
                'follow_location'  => 1,
                'max_redirects'    => 5,
                'user_agent'       => 'Atomni-RSS-Importer/1.0 (+' . url('/') . ')',
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $xml = @file_get_contents($url, false, $context);

        if ($xml === false || empty(trim($xml))) {
            throw new \RuntimeException("Could not fetch URL or empty response.");
        }

        // Suppress XML parse errors
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_clear_errors();

        if ($feed === false) {
            throw new \RuntimeException("Could not parse XML from feed.");
        }

        // ── RSS 2.0 ──────────────────────────────────────────────────
        if (isset($feed->channel)) {
            return $this->parseRss2($feed);
        }

        // ── Atom ─────────────────────────────────────────────────────
        if (isset($feed->entry)) {
            return $this->parseAtom($feed);
        }

        throw new \RuntimeException("Unrecognised feed format (not RSS 2.0 or Atom).");
    }

    private function parseRss2(\SimpleXMLElement $feed): array
    {
        $items  = [];
        $ns     = $feed->getNamespaces(true);
        $hasDc  = isset($ns['dc']);
        $hasMedia = isset($ns['media']);
        $hasContent = isset($ns['content']);

        foreach ($feed->channel->item as $item) {
            $image = null;

            // media:content or media:thumbnail
            if ($hasMedia) {
                $media = $item->children($ns['media']);
                if (isset($media->content)) {
                    $image = (string) ($media->content->attributes()['url'] ?? '');
                } elseif (isset($media->thumbnail)) {
                    $image = (string) ($media->thumbnail->attributes()['url'] ?? '');
                }
            }

            // enclosure
            if (!$image && isset($item->enclosure)) {
                $encType = (string) $item->enclosure->attributes()['type'];
                if (str_starts_with($encType, 'image/')) {
                    $image = (string) $item->enclosure->attributes()['url'];
                }
            }

            // Fallback: scrape first <img> from description HTML
            if (!$image) {
                $desc = (string) ($item->description ?? '');
                if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $desc, $m)) {
                    $image = $m[1];
                }
            }

            // content:encoded
            $content = '';
            if ($hasContent) {
                $contentNs = $item->children($ns['content']);
                $content   = (string) ($contentNs->encoded ?? '');
            }

            // category
            $category = '';
            if (isset($item->category)) {
                $category = (string) $item->category;
            } elseif ($hasDc) {
                $dc = $item->children($ns['dc']);
                $category = (string) ($dc->subject ?? '');
            }

            $items[] = [
                'title'       => str_replace(['&amp;#039;', '&#039;', '&#039', '&amp;#39;', '&#39;', '&#39'], "'", html_entity_decode(strip_tags((string) $item->title), ENT_QUOTES, 'UTF-8')),
                'link'        => (string) $item->link,
                'description' => str_replace(['&amp;#039;', '&#039;', '&#039', '&amp;#39;', '&#39;', '&#39'], "'", html_entity_decode(strip_tags((string) $item->description), ENT_QUOTES, 'UTF-8')),
                'content'     => $content,
                'image'       => $image ?: null,
                'category'    => $category,
                'pubDate'     => (string) $item->pubDate,
            ];
        }

        return $items;
    }

    private function parseAtom(\SimpleXMLElement $feed): array
    {
        $items = [];
        $ns    = $feed->getNamespaces(true);

        foreach ($feed->entry as $entry) {
            // Get the 'alternate' link href
            $link = '';
            foreach ($entry->link as $l) {
                $rel = (string) ($l->attributes()['rel'] ?? 'alternate');
                if ($rel === 'alternate' || $rel === '') {
                    $link = (string) ($l->attributes()['href'] ?? '');
                    break;
                }
            }

            $summary = (string) ($entry->summary ?? '');
            $content = (string) ($entry->content ?? '');

            // Image from media namespace
            $image = null;
            if (isset($ns['media'])) {
                $media = $entry->children($ns['media']);
                if (isset($media->thumbnail)) {
                    $image = (string) ($media->thumbnail->attributes()['url'] ?? '');
                } elseif (isset($media->content)) {
                    $image = (string) ($media->content->attributes()['url'] ?? '');
                }
            }

            // Category
            $category = '';
            if (isset($entry->category)) {
                $category = (string) ($entry->category->attributes()['term'] ?? $entry->category);
            }

            $items[] = [
                'title'       => str_replace(['&amp;#039;', '&#039;', '&#039', '&amp;#39;', '&#39;', '&#39'], "'", html_entity_decode(strip_tags((string) $entry->title), ENT_QUOTES, 'UTF-8')),
                'link'        => $link,
                'description' => str_replace(['&amp;#039;', '&#039;', '&#039', '&amp;#39;', '&#39;', '&#39'], "'", html_entity_decode(strip_tags($summary ?: $content), ENT_QUOTES, 'UTF-8')),
                'content'     => $content,
                'image'       => $image ?: null,
                'category'    => $category,
                'pubDate'     => (string) ($entry->published ?? $entry->updated ?? ''),
            ];
        }

        return $items;
    }

    /**
     * Find or create a category by name. Returns null if name is empty.
     */
    private function resolveCategory(?string $name): ?int
    {
        if (empty($name)) {
            // Fallback to first available category
            return Category::first()?->id;
        }

        $category = Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => ucwords(strtolower(trim($name)))]
        );

        return $category->id;
    }
}
