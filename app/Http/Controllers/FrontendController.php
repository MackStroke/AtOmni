<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\Setting;
use App\Models\HomepageSection;
use App\Models\Comment;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * About page
     */
    public function about()
    {
        $teamMembers = TeamMember::where('is_active', true)->orderBy('order_column')->get();
        return view('pages.about', compact('teamMembers'));
    }

    /**
     * Donate page
     */
    public function donate()
    {
        $donors = \App\Models\Donor::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('donated_at')
            ->get();
            
        return view('pages.donate', compact('donors'));
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $page = \App\Models\Page::where('slug', 'contact')->first();
        return view('pages.contact', compact('page'));
    }

    /**
     * Homepage — Featured + Latest + Trending + Editor's Picks
     */
    public function home()
    {
        // Featured articles (top 5 most recent featured posts)
        $featuredPosts = Post::published()
            ->featured()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(5)
            ->get();

        // Secondary hero articles (next 4 latest, excluding featured)
        $featuredPostIds = $featuredPosts->pluck('id')->toArray();
        $secondaryPosts = Post::published()
            ->with(['author', 'category'])
            ->when(!empty($featuredPostIds), fn($q) => $q->whereNotIn('id', $featuredPostIds))
            ->latest('published_at')
            ->take(4)
            ->get();

        // Fetch dynamic homepage sections
        $dynamicSections = HomepageSection::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Latest articles (paginated, for the main feed)
        $latestPosts = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        // Editor's Picks (3 most-viewed posts)
        $editorPicks = Post::published()
            ->with(['author', 'category'])
            ->orderByDesc('views_count')
            ->take(3)
            ->get();

        // Categories for filter pills
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get();

        // Locations for filtering dropdown
        $locations = \App\Models\Location::with('children')->whereNull('parent_id')->orderBy('name')->get();

        // Selected Author Section
        $authorSectionEnabled = \App\Models\Setting::get('home_author_section_enabled') == '1';
        $authorSectionTitle = \App\Models\Setting::get('home_author_section_title', 'Selected Author');
        $authorSectionPosts = collect();
        if ($authorSectionEnabled) {
            $authorId = \App\Models\Setting::get('home_author_section_author_id');
            if ($authorId) {
                $authorSectionPosts = Post::published()
                    ->where('author_id', $authorId)
                    ->with(['author', 'category'])
                    ->latest('published_at')
                    ->take(6)
                    ->get();
            }
        }

        // Specific Category Sections (below Editor's Picks)
        $targetCategorySlugs = ['health', 'sports', 'entertainment', 'lifestyle', 'tech', 'world'];
        $categorySections = Category::whereIn('slug', $targetCategorySlugs)
            ->with(['posts' => function($q) {
                $q->published()->with('author')->latest('published_at')->take(6);
            }])
            ->get()
            ->sortBy(function($cat) use ($targetCategorySlugs) {
                return array_search($cat->slug, $targetCategorySlugs);
            });

        // Short Videos Section
        $shortVideos = Post::whereHas('category', function($q) {
                $q->where('slug', 'short-videos');
            })->published()->with('category')->latest('published_at')->take(10)->get();

        // Top Viral Videos Section (Long Form)
        $viralVideos = Post::whereHas('category', function($q) {
                $q->where('slug', 'viral-videos');
            })->published()->with('category')->latest('published_at')->take(5)->get();

        return view('home', compact(
            'featuredPosts',
            'secondaryPosts',
            'latestPosts',
            'editorPicks',
            'categories',
            'locations',
            'authorSectionEnabled',
            'authorSectionTitle',
            'authorSectionPosts',
            'categorySections',
            'shortVideos',
            'viralVideos',
            'dynamicSections',
        ));
    }

    /**
     * Category page — paginated posts for a category
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return redirect()->route('search', ['q' => str_replace('-', ' ', $slug)])
                ->with('info', 'No category found for "' . str_replace('-', ' ', $slug) . '". Showing search results instead.');
        }

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        // All categories for the pills
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get();

        $popularTags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(6)
            ->get();

        $fixtures = [];
        if ($slug === 'sports') {
            $fixtures = \App\Models\SportsFixture::orderBy('match_time', 'asc')->take(12)->get();
        }

        return view('pages.category', compact('category', 'posts', 'slug', 'categories', 'popularTags', 'fixtures'));
    }

    /**
     * Location page — paginated posts for a specific location
     */
    public function location(string $slug)
    {
        $location = \App\Models\Location::where('slug', $slug)->first();

        if (!$location) {
            return redirect()->route('search', ['q' => str_replace('-', ' ', $slug)])
                ->with('info', 'No location found for "' . str_replace('-', ' ', $slug) . '". Showing search results instead.');
        }

        $posts = Post::published()
            ->whereHas('locations', function ($query) use ($location) {
                $query->where('locations.id', $location->id)
                      ->orWhere('locations.parent_id', $location->id);
            })
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get();

        $popularTags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(6)
            ->get();

        return view('pages.location', compact('location', 'posts', 'slug', 'categories', 'popularTags'));
    }

    /**
     * Article page — display a single post
     */
    public function article(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->with(['author', 'category', 'tags'])
            ->firstOrFail();

        // Check for kill switch or unpublished status
        if ($post->kill_switch || !in_array($post->status, ['published', 'scheduled']) || $post->published_at > now()) {
            if ($post->kill_switch && $post->redirect_url) {
                return redirect()->away($post->redirect_url);
            }
            if ($post->kill_switch) {
                return redirect()->route('home')->with('error', 'This article is no longer available.');
            }
            abort(404);
        }

        // Increment views
        $post->increment('views_count');

        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // Load approved comments
        $comments = $post->comments()
            ->approved()
            ->topLevel()
            ->with('replies')
            ->latest()
            ->get();

        return view('pages.article', compact('post', 'relatedPosts', 'comments'));
    }

    /**
     * Store new comment for an article
     */
    public function storeComment(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'comment_text' => 'required|string|max:1000',
        ]);

        $comment = new Comment([
            'post_id' => $post->id,
            'guest_name' => strip_tags($validated['guest_name']),
            'guest_email' => $validated['guest_email'],
            'comment_text' => strip_tags($validated['comment_text']),
            'is_approved' => false,
        ]);

        if (auth()->check()) {
            $comment->user_id = auth()->id();
        }

        $comment->save();

        return redirect()->back()->with('success', 'Your comment has been submitted and is awaiting approval.');
    }

    /**
     * Search page — full-text search on posts
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $tag = $request->input('tag', '');

        $postsQuery = Post::published()->with(['author', 'category']);

        if ($tag) {
            // Exact/like match for a specific tag click
            $postsQuery->whereHas('tags', fn($q) => $q->where('name', 'like', "%{$tag}%"));
        } elseif ($query) {
            $postsQuery->where(function ($q) use ($query) {
                // 1. Search the exact phrase first
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$query}%"));

                // 2. Split query into words to find articles that contain the words anywhere
                $words = array_filter(explode(' ', $query), fn($w) => strlen($w) > 2);
                
                if (count($words) > 1) {
                    $q->orWhere(function ($multiWordQuery) use ($words) {
                        foreach ($words as $word) {
                            $multiWordQuery->where(function ($subQ) use ($word) {
                                $subQ->where('title', 'like', "%{$word}%")
                                     ->orWhere('excerpt', 'like', "%{$word}%")
                                     ->orWhere('content', 'like', "%{$word}%")
                                     ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$word}%"));
                            });
                        }
                    });
                }
            });
        }

        $results = $postsQuery->latest('published_at')->paginate(12);

        // Trending tags for suggestions
        $trendingTags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(6)
            ->get();

        return view('pages.search', compact('results', 'query', 'tag', 'trendingTags'));
    }

    /**
     * API: Live search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $postsQuery = Post::published()->select('id', 'title', 'slug', 'published_at');

        $postsQuery->where(function ($q) use ($query) {
            // 1. Exact phrase in title
            $q->where('title', 'like', "%{$query}%");
            
            // 2. Exact phrase in other fields
            $q->orWhere('excerpt', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$query}%"));

            // 3. Multi-word search
            $words = array_filter(explode(' ', $query), fn($w) => strlen($w) > 2);
            if (count($words) > 1) {
                $q->orWhere(function ($multiWordQuery) use ($words) {
                    foreach ($words as $word) {
                        $multiWordQuery->where(function ($subQ) use ($word) {
                            $subQ->where('title', 'like', "%{$word}%")
                                 ->orWhere('excerpt', 'like', "%{$word}%")
                                 ->orWhere('content', 'like', "%{$word}%")
                                 ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$word}%"));
                        });
                    }
                });
            }
        });

        // Get up to 6 results
        $results = $postsQuery->latest('published_at')->take(6)->get()->map(function($post) {
            return [
                'title' => $post->title,
                'url' => url('article/' . $post->slug),
                'date' => $post->published_at ? $post->published_at->format('M j, Y') : ''
            ];
        });

        return response()->json($results);
    }

    /**
     * Explore page — searchable topics and categories
     */
    public function explore(Request $request)
    {
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get();

        $tags = Tag::withCount(['posts' => fn($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get();

        return view('pages.explore', compact('categories', 'tags'));
    }

    /**
     * Dynamic legal/static page from database
     */
    public function legalPage(string $slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_published', true)->first();

        if (!$page) {
            return redirect()->route('search', ['q' => str_replace('-', ' ', $slug)])
                ->with('info', 'Page not found. Showing search results instead.');
        }

        return view('pages.legal.dynamic', compact('page'));
    }

    /**
     * Generate XML Sitemap — served from cache, auto-invalidated by model observers.
     */
    public function sitemap()
    {
        $content = \Illuminate\Support\Facades\Cache::store('file')->remember('sitemap_xml', now()->addHours(24), function () {
            $posts = \App\Models\Post::published()
                ->select('slug', 'updated_at', 'published_at')
                ->orderByDesc('published_at')
                ->get();

            $categories = \App\Models\Category::select('slug', 'updated_at')->get();

            $pages = \App\Models\Page::where('is_published', true)
                ->select('slug', 'updated_at')
                ->get();

            $jobs = \App\Models\JobPosting::where('status', 'active')
                ->select('slug', 'updated_at')
                ->get();

            return view('sitemap', compact('posts', 'categories', 'pages', 'jobs'))->render();
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
