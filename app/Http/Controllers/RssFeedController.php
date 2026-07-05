<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RssFeedController extends Controller
{
    /**
     * Serve the site's RSS 2.0 feed.
     * Cached for 30 minutes — automatically invalidated via SitemapObserver
     * (or cleared when a post is published/updated).
     */
    public function index(): Response
    {
        $enabled = Setting::get('rss_enabled', 'true');
        if ($enabled !== 'true') {
            abort(404);
        }

        $xml = Cache::store('file')->remember('rss_feed_xml', now()->addMinutes(30), function () {
            $maxItems = (int) Setting::get('rss_max_items', 25);
            $posts    = Post::published()
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take($maxItems)
                ->get();

            $feedTitle       = e(Setting::get('rss_title', 'Atomni News Feed'));
            $feedDescription = e(Setting::get('rss_description', 'Latest news and articles from Atomni'));
            $feedUrl         = url('/feed.xml');
            $siteUrl         = url('/');
            $buildDate       = now()->toRfc1123String();
            $lastBuildDate   = $posts->first()?->published_at?->toRfc1123String() ?? $buildDate;
            $siteLogo        = Setting::get('site_logo');

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $xml .= '<rss version="2.0"' . PHP_EOL;
            $xml .= '     xmlns:atom="http://www.w3.org/2005/Atom"' . PHP_EOL;
            $xml .= '     xmlns:media="http://search.yahoo.com/mrss/"' . PHP_EOL;
            $xml .= '     xmlns:dc="http://purl.org/dc/elements/1.1/"' . PHP_EOL;
            $xml .= '     xmlns:content="http://purl.org/rss/1.0/modules/content/">' . PHP_EOL;
            $xml .= '  <channel>' . PHP_EOL;
            $xml .= "    <title>{$feedTitle}</title>" . PHP_EOL;
            $xml .= "    <link>{$siteUrl}</link>" . PHP_EOL;
            $xml .= "    <description>{$feedDescription}</description>" . PHP_EOL;
            $xml .= '    <language>en-in</language>' . PHP_EOL;
            $xml .= "    <lastBuildDate>{$lastBuildDate}</lastBuildDate>" . PHP_EOL;
            $xml .= "    <pubDate>{$buildDate}</pubDate>" . PHP_EOL;
            $xml .= '    <ttl>30</ttl>' . PHP_EOL;
            $xml .= '    <generator>Atomni CMS</generator>' . PHP_EOL;
            $xml .= "    <atom:link href=\"{$feedUrl}\" rel=\"self\" type=\"application/rss+xml\"/>" . PHP_EOL;

            if ($siteLogo) {
                $siteLogoUrl = asset('storage/' . ltrim($siteLogo, '/'));
                $xml .= '    <image>' . PHP_EOL;
                $xml .= "      <url>{$siteLogoUrl}</url>" . PHP_EOL;
                $xml .= "      <title>{$feedTitle}</title>" . PHP_EOL;
                $xml .= "      <link>{$siteUrl}</link>" . PHP_EOL;
                $xml .= '    </image>' . PHP_EOL;
            }

            foreach ($posts as $post) {
                $postUrl    = route('frontend.article', $post->slug);
                $pubDate    = $post->published_at->toRfc1123String();
                $postTitle  = e($post->title);
                $postImgUrl = $post->featuredImageUrl();
                $description = e(
                    $post->excerpt
                        ? $post->excerpt
                        : Str::limit(strip_tags($post->content), 300)
                );

                $xml .= '    <item>' . PHP_EOL;
                $xml .= "      <title>{$postTitle}</title>" . PHP_EOL;
                $xml .= "      <link>{$postUrl}</link>" . PHP_EOL;
                $xml .= "      <guid isPermaLink=\"true\">{$postUrl}</guid>" . PHP_EOL;
                $xml .= "      <pubDate>{$pubDate}</pubDate>" . PHP_EOL;

                if ($post->author) {
                    $xml .= '      <dc:creator>' . e($post->author->name) . '</dc:creator>' . PHP_EOL;
                }
                if ($post->category) {
                    $xml .= '      <category>' . e($post->category->name) . '</category>' . PHP_EOL;
                }

                $xml .= "      <description>{$description}</description>" . PHP_EOL;
                $xml .= '      <content:encoded><![CDATA[' . $post->content . ']]></content:encoded>' . PHP_EOL;

                if ($postImgUrl && !str_ends_with($postImgUrl, 'atomni-placeholder.svg')) {
                    $xml .= "      <enclosure url=\"{$postImgUrl}\" type=\"image/jpeg\" length=\"0\"/>" . PHP_EOL;
                    $xml .= "      <media:content url=\"{$postImgUrl}\" medium=\"image\"/>" . PHP_EOL;
                }

                $xml .= '    </item>' . PHP_EOL;
            }

            $xml .= '  </channel>' . PHP_EOL;
            $xml .= '</rss>' . PHP_EOL;

            return $xml;
        });

        return response($xml, 200, [
            'Content-Type'  => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=1800',
        ]);
    }
}
