<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrafficReport;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get date range
        $startDate = $request->get('start_date', now()->subDays(14)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $trafficData = TrafficReport::whereBetween('report_date', [$startDate, $endDate])
            ->orderBy('report_date', 'asc')
            ->get();

        $trafficHourlyData = \App\Models\TrafficReportHourly::whereBetween('report_date', [$startDate, $endDate])
            ->orderBy('report_date', 'asc')
            ->orderBy('hour', 'asc')
            ->get();

        // Calculate totals
        $totals = [
            'page_views' => $trafficData->sum('page_views'),
            'unique_visitors' => $trafficData->sum('unique_visitors'),
            'data_consumed_mb' => $trafficData->sum('data_consumed_mb'),
        ];

        // Chart data
        $chartData = [
            'labels' => $trafficData->pluck('report_date')->map(fn($date) => $date->format('M j'))->toArray(),
            'views' => $trafficData->pluck('page_views')->toArray(),
            'visitors' => $trafficData->pluck('unique_visitors')->toArray(),
        ];

        // Hourly Chart Data
        $chartHourlyData = [
            'labels' => $trafficHourlyData->map(fn($t) => \Carbon\Carbon::parse($t->report_date)->format('M j') . ' ' . str_pad($t->hour, 2, '0', STR_PAD_LEFT) . ':00')->toArray(),
            'views' => $trafficHourlyData->pluck('page_views')->toArray(),
            'visitors' => $trafficHourlyData->pluck('unique_visitors')->toArray(),
        ];

        // Top Trending Articles (by real views_count, filtered by date)
        $trendingPosts = Post::published()
            ->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('category')
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        // Trending Tags (by real post count in date range)
        $trendingTags = Tag::withCount(['posts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();

        // ── Real Content Stats (filtered by date) ────────────────────────────────────
        $totalPosts = Post::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $publishedPosts = Post::where('status', 'published')
            ->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $draftPosts = Post::where('status', 'draft')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $totalComments = Comment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $totalCategories = Category::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();

        // Real aggregate: impressions = sum of all post view counts in date range
        $totalImpressions = Post::whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('views_count');
        // No real click tracking yet — show actual post views as a more honest metric
        $totalClicks = $totals['page_views']; 
        $uniqueVisitorsSearch = $totals['unique_visitors'];

        // Average views per post
        $avgViewsPerPost = $publishedPosts > 0 ? round($totalImpressions / $publishedPosts) : 0;

        // Top Keywords (tags with most posts in date range)
        $topKeywords = Tag::withCount(['posts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->orderBy('posts_count', 'desc')
            ->take(3)
            ->get();

        // Popular Authors in date range
        $popularAuthors = User::withCount(['posts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }])
            ->withSum(['posts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('published_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }], 'views_count')
            ->orderBy('posts_sum_views_count', 'desc')
            ->take(3)
            ->get();

        // ── Advanced Visitor Sessions (New Internal Tracker) ──────────────────────
        $sessionsQuery = \App\Models\VisitorSession::whereBetween('started_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $totalSessions = $sessionsQuery->count();
        
        // Calculate average session length in seconds
        $sessionLengths = \App\Models\VisitorSession::whereBetween('started_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('TIMESTAMPDIFF(SECOND, started_at, last_activity_at) as duration')
            ->pluck('duration');
            
        $avgVisitLengthSeconds = $sessionLengths->count() > 0 ? round($sessionLengths->avg()) : 0;
        
        // Format visit length (e.g., "1m 25s" or "45s")
        if ($avgVisitLengthSeconds >= 60) {
            $minutes = floor($avgVisitLengthSeconds / 60);
            $seconds = $avgVisitLengthSeconds % 60;
            $formattedVisitLength = "{$minutes}m {$seconds}s";
        } else {
            $formattedVisitLength = "{$avgVisitLengthSeconds}s";
        }

        // Visits per visitor (Sessions / Unique Visitors)
        $visitsPerVisitor = $totals['unique_visitors'] > 0 ? round($totalSessions / $totals['unique_visitors'], 1) : 1.0;
        
        // New vs Returning Visitors
        $newVisitors = (clone $sessionsQuery)->where('is_new_visitor', true)->count();
        $returningVisitors = (clone $sessionsQuery)->where('is_new_visitor', false)->count();

        // Channels (Pie Chart Data)
        $channelsData = (clone $sessionsQuery)
            ->selectRaw('channel, count(*) as count')
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();
            
        // Top engaged traffic source (Channel with most sessions)
        $topChannel = !empty($channelsData) ? array_keys($channelsData, max($channelsData))[0] : 'Organic Search';
        $topChannelPercentage = $totalSessions > 0 ? round(($channelsData[$topChannel] ?? 0) / $totalSessions * 100) : 0;

        // Top Referrers (used for "Top Search Queries" placeholder)
        $topReferrers = (clone $sessionsQuery)
            ->selectRaw('referrer, count(*) as clicks')
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderBy('clicks', 'desc')
            ->take(10)
            ->get();

        // ── Smart AI Reporting ────────────────────────────────────
        $aiSummary = Cache::remember('reports_ai_summary_' . $startDate . '_' . $endDate, 3600 * 12, function () use ($totals, $totalPosts, $uniqueVisitorsSearch) {
            $geminiKey = Setting::where('key', 'gemini_key')->value('value');
            $openAiKey = Setting::where('key', 'open_ai_key')->value('value');
            $anthropicKey = Setting::where('key', 'anthropic_key')->value('value');
            
            $prompt = "You are an AI data analyst for a news platform. Provide a brief, engaging 2-to-3 sentence summary of these metrics. Speak directly to the site admin. Do not use markdown. Metrics: Page Views: " . number_format($totals['page_views']) . ", Unique Visitors: " . number_format($totals['unique_visitors']) . ", Published Posts: {$totalPosts}. Mention the performance positively and give a brief actionable tip.";

            try {
                // Auto-pick best available: Gemini > Anthropic > OpenAI
                if (!empty($geminiKey)) {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(20)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]);
                    return $response->json('candidates.0.content.parts.0.text') ?? 'AI insights are currently unavailable. Ensure your API key is valid.';
                } elseif (!empty($anthropicKey)) {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(10)
                        ->withHeaders([
                            'x-api-key' => $anthropicKey,
                            'anthropic-version' => '2023-06-01',
                            'content-type' => 'application/json',
                        ])
                        ->post('https://api.anthropic.com/v1/messages', [
                            'model' => 'claude-3-5-sonnet-20241022',
                            'max_tokens' => 512,
                            'messages' => [['role' => 'user', 'content' => $prompt]],
                        ]);
                    return $response->json('content.0.text') ?? 'AI insights are currently unavailable. Ensure your API key is valid.';
                } elseif (!empty($openAiKey)) {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(10)->withToken($openAiKey)->post("https://api.openai.com/v1/chat/completions", [
                        'model' => 'gpt-4o',
                        'messages' => [['role' => 'user', 'content' => $prompt]]
                    ]);
                    return $response->json('choices.0.message.content') ?? 'AI insights are currently unavailable. Ensure your API key is valid.';
                }
            } catch (\Exception $e) {}
            return "Connect an AI API key (Gemini/OpenAI/Anthropic) in Settings > Integrations to view real-time smart performance insights here!";
        });

        return view('admin.reports.index', compact(
            'trafficData', 'totals', 'chartData', 'chartHourlyData', 'trendingPosts', 'trendingTags', 'startDate', 'endDate',
            'totalImpressions', 'totalClicks', 'uniqueVisitorsSearch', 'topKeywords', 'popularAuthors', 
            'totalPosts', 'publishedPosts', 'draftPosts', 'totalComments', 'totalCategories', 'avgViewsPerPost', 'aiSummary',
            'formattedVisitLength', 'visitsPerVisitor', 'newVisitors', 'returningVisitors', 'channelsData', 'topChannel', 'topChannelPercentage', 'totalSessions', 'topReferrers'
        ));
    }
}

