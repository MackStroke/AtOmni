<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected function buildFilteredQuery(Request $request)
    {
        $query = Post::query();

        // Authors only see their own posts
        if (auth()->user()->role === 'author') {
            $query->where('author_id', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($tags = $request->input('tags')) {
            $tagsArray = is_array($tags) ? $tags : [$tags];
            $query->whereHas('tags', function($q) use ($tagsArray) {
                $q->whereIn('tags.id', $tagsArray);
            });
        }

        $applyScoreFilter = function($query, $column, $value) {
            if ($value === 'best')  $query->where($column, '>=', 90);
            if ($value === 'good')  $query->whereBetween($column, [70, 89]);
            if ($value === 'bad')   $query->whereBetween($column, [50, 69]);
            if ($value === 'worse') $query->whereNotNull($column)->where($column, '<', 50);
        };

        if ($seoFilter = $request->input('seo_filter')) {
            $applyScoreFilter($query, 'seo_score', $seoFilter);
        }
        if ($aeoFilter = $request->input('aeo_filter')) {
            $applyScoreFilter($query, 'aeo_score', $aeoFilter);
        }
        if ($geoFilter = $request->input('geo_filter')) {
            $applyScoreFilter($query, 'geo_score', $geoFilter);
        }

        if ($request->filled('author_id')) {
            // Only apply if the user isn't an author trying to view other authors
            if (auth()->user()->role !== 'author' || auth()->id() == $request->input('author_id')) {
                $query->where('author_id', $request->input('author_id'));
            }
        }

        if ($request->filled('source')) {
            $query->where('is_rss', $request->input('source') === 'rss');
        }

        if ($dateFilter = $request->input('date_filter')) {
            $now = \Carbon\Carbon::now();
            $from = null;
            $to = null;

            switch ($dateFilter) {
                case 'today':
                    $from = $now->copy()->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'yesterday':
                    $from = $now->copy()->subDay()->startOfDay();
                    $to = $now->copy()->subDay()->endOfDay();
                    break;
                case 'last_7_days':
                    $from = $now->copy()->subDays(6)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_30_days':
                    $from = $now->copy()->subDays(29)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'this_month':
                    $from = $now->copy()->startOfMonth();
                    $to = $now->copy()->endOfMonth();
                    break;
                case 'last_month':
                    $from = $now->copy()->subMonth()->startOfMonth();
                    $to = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'custom':
                    if ($dateFrom = $request->input('date_from')) {
                        try { $from = \Carbon\Carbon::parse($dateFrom)->startOfDay(); } catch (\Exception $e) {}
                    }
                    if ($dateTo = $request->input('date_to')) {
                        try { $to = \Carbon\Carbon::parse($dateTo)->endOfDay(); } catch (\Exception $e) {}
                    }
                    break;
            }

            if ($from || $to) {
                $query->where(function($q) use ($from, $to) {
                    $q->where(function($qPublished) use ($from, $to) {
                        $qPublished->whereNotNull('published_at');
                        if ($from) $qPublished->where('published_at', '>=', $from);
                        if ($to) $qPublished->where('published_at', '<=', $to);
                    })->orWhere(function($qCreated) use ($from, $to) {
                        $qCreated->whereNull('published_at');
                        if ($from) $qCreated->where('created_at', '>=', $from);
                        if ($to) $qCreated->where('created_at', '<=', $to);
                    });
                });
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request)->with('category', 'author');

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $posts = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $authors = auth()->user()->role === 'author'
            ? \App\Models\User::where('id', auth()->id())->get()
            : \App\Models\User::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories', 'tags', 'authors'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        // Authors can only set themselves as author
        $authors = auth()->user()->role === 'author'
            ? \App\Models\User::where('id', auth()->id())->get()
            : \App\Models\User::orderBy('name')->get();
        $locations = \App\Models\Location::with('children')->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'tags', 'authors', 'locations'));
    }

    public function store(Request $request)
    {
        // Authors are always set as the author of their own posts
        if (auth()->user()->role === 'author') {
            $request->merge(['author_id' => auth()->id()]);
        }

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'excerpt'        => 'nullable|string|max:500',
            'tldr'           => 'nullable|string|max:300',
            'category_id'    => 'nullable',
            'author_id'      => 'required|exists:users,id',
            'status'         => 'required|in:draft,published,scheduled',
            'is_featured'    => 'boolean',
            'featured_until' => 'nullable|date',
            'featured_image' => 'nullable|string|max:500',
            'tags'           => 'nullable|array',
            'locations'      => 'nullable|array',
            'faqs'           => 'nullable|array',
            'published_at'   => 'nullable|date|after:now',
            'kill_switch'    => 'boolean',
            'redirect_url'   => 'nullable|url|max:255',
        ]);

        if ($validated['status'] === 'scheduled' && empty($validated['published_at'])) {
            return back()->withErrors(['published_at' => 'A future publish date is required when status is Scheduled.'])->withInput();
        }

        $validated['slug']         = Str::slug($validated['title']);
        $validated['reading_time'] = Post::calculateReadingTime($validated['content']);
        $validated['is_featured']  = $request->boolean('is_featured');
        if ($validated['is_featured']) {
            $validated['featured_until'] = $request->input('featured_until') ?: now()->addDays(2);
        } else {
            $validated['featured_until'] = null;
        }
        $validated['kill_switch']  = $request->boolean('kill_switch');

        // Force draft status if kill switch is active
        if ($validated['kill_switch']) {
            $validated['status'] = 'draft';
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Auto-categorize or Create category if it's not a number
        if (empty($validated['category_id'])) {
            $validated['category_id'] = $this->autoAssignCategory($validated['title'], $validated['content']);
        } elseif (!is_numeric($validated['category_id'])) {
            $newCat = Category::firstOrCreate(['name' => $validated['category_id']], ['slug' => Str::slug($validated['category_id'])]);
            $validated['category_id'] = $newCat->id;
        }

        $post = Post::create($validated);

        if ($request->has('tags') && !empty($request->tags)) {
            $tagIds = collect($request->input('tags'))->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)])->id;
            });
            $post->tags()->sync($tagIds);
        } else {
            // Auto-tag if no tags provided
            $this->autoAssignTags($post);
        }

        if ($request->has('locations')) {
            $post->locations()->sync($request->input('locations'));
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        // Authors can only edit their own posts
        if (auth()->user()->role === 'author' && $post->author_id !== auth()->id()) {
            abort(403, 'You can only edit your own posts.');
        }

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $authors = auth()->user()->role === 'author'
            ? \App\Models\User::where('id', auth()->id())->get()
            : \App\Models\User::orderBy('name')->get();
        $locations = \App\Models\Location::with('children')->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'authors', 'locations'));
    }

    public function update(Request $request, Post $post)
    {
        // Authors can only update their own posts
        if (auth()->user()->role === 'author' && $post->author_id !== auth()->id()) {
            abort(403, 'You can only edit your own posts.');
        }
        if (auth()->user()->role === 'author') {
            $request->merge(['author_id' => auth()->id()]);
        }

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'excerpt'        => 'nullable|string|max:500',
            'tldr'           => 'nullable|string|max:300',
            'category_id'    => 'nullable',
            'author_id'      => 'required|exists:users,id',
            'status'         => 'required|in:draft,published,scheduled',
            'is_featured'    => 'boolean',
            'featured_until' => 'nullable|date',
            'featured_image' => 'nullable|string|max:500',
            'tags'           => 'nullable|array',
            'locations'      => 'nullable|array',
            'faqs'           => 'nullable|array',
            'published_at'   => 'nullable|date',
            'kill_switch'    => 'boolean',
            'redirect_url'   => 'nullable|url|max:255',
        ]);

        if ($validated['status'] === 'scheduled') {
            if (empty($validated['published_at'])) {
                return back()->withErrors(['published_at' => 'A future publish date is required when status is Scheduled.'])->withInput();
            } elseif (\Carbon\Carbon::parse($validated['published_at'])->isPast()) {
                if ($post->status !== 'scheduled' || $validated['published_at'] !== $post->published_at?->format('Y-m-d\TH:i')) {
                    return back()->withErrors(['published_at' => 'Scheduled publish date must be in the future.'])->withInput();
                }
            }
        }

        $validated['slug']         = Str::slug($validated['title']);
        $validated['reading_time'] = Post::calculateReadingTime($validated['content']);
        $validated['is_featured']  = $request->boolean('is_featured');
        if ($validated['is_featured']) {
            $validated['featured_until'] = $request->input('featured_until') ?: now()->addDays(2);
        } else {
            $validated['featured_until'] = null;
        }
        $validated['kill_switch']  = $request->boolean('kill_switch');

        // Force draft status if kill switch is active
        if ($validated['kill_switch']) {
            $validated['status'] = 'draft';
        }

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        // Auto-categorize or Create category if it's not a number
        if (empty($validated['category_id'])) {
            $validated['category_id'] = $this->autoAssignCategory($validated['title'], $validated['content']);
        } elseif (!is_numeric($validated['category_id'])) {
            $newCat = Category::firstOrCreate(['name' => $validated['category_id']], ['slug' => Str::slug($validated['category_id'])]);
            $validated['category_id'] = $newCat->id;
        }

        $post->update($validated);

        if ($request->has('tags') && !empty($request->tags)) {
            $tagIds = collect($request->input('tags'))->map(function ($tagName) {
                return Tag::firstOrCreate(['name' => $tagName], ['slug' => Str::slug($tagName)])->id;
            });
            $post->tags()->sync($tagIds);
        } else {
            // Auto-tag if no tags provided
            $this->autoAssignTags($post);
        }

        if ($request->has('locations')) {
            $post->locations()->sync($request->input('locations'));
        } else {
            $post->locations()->detach();
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    // ── Content Auto-Analyze Helpers ─────────────────────────────────────
    
    public function autoTaxonomy(Request $request, \App\Services\TaxonomyService $taxonomyService)
    {
        $title = $request->input('title', '');
        $content = $request->input('content', '');
        
        $result = $taxonomyService->suggestForPost($title, $content);
        
        return response()->json([
            'category_id' => $result['category_id'] ?? null,
            'tags' => $result['tags'] ?? [],
            'locations' => $result['locations'] ?? []
        ]);
    }

    private function autoAssignCategory($title, $content)
    {
        // Pluck all categories from DB and search for matches in content/title
        $categories = Category::all();
        $text = strtolower($title . ' ' . strip_tags($content));
        
        foreach ($categories as $cat) {
            if (str_contains($text, strtolower($cat->name))) {
                return $cat->id;
            }
        }
        
        // Return first category as fallback if unable to match (or null)
        return $categories->first()?->id;
    }

    private function autoAssignTags(Post $post)
    {
        // Simple auto-tagging based on common words in the title/content vs the tags DB
        $tags = Tag::all();
        $text = strtolower($post->title . ' ' . strip_tags($post->content));
        $matchedTags = [];

        foreach ($tags as $tag) {
            if (str_contains($text, strtolower($tag->name))) {
                $matchedTags[] = $tag->id;
            }
        }

        if (!empty($matchedTags)) {
            $post->tags()->sync($matchedTags);
        }
    }

    public function destroy(Post $post)
    {
        // Authors can only delete their own posts
        if (auth()->user()->role === 'author' && $post->author_id !== auth()->id()) {
            abort(403, 'You can only delete your own posts.');
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post moved to trash.');
    }

    public function kill(Request $request, Post $post)
    {
        // Only editors and above can kill/restore posts
        if (auth()->user()->role === 'author') {
            abort(403, 'Authors cannot use the kill switch.');
        }

        $request->validate([
            'redirect_url' => 'nullable|url|max:255'
        ]);

        $isKilling = !$post->kill_switch;
        $post->update([
            'kill_switch' => $isKilling,
            'redirect_url' => $isKilling ? $request->redirect_url : null,
            'status' => $isKilling ? 'draft' : $post->status, // Fallback to draft to ensure it drops from standard queries
        ]);

        $message = $isKilling ? 'Post taken down successfully.' : 'Post restored successfully.';
        return back()->with('success', $message);
    }

    public function checkPlagiarism(Request $request, \App\Services\ContentAnalysisService $analysisService)
    {
        $request->validate([
            'sentences' => 'required|array|max:20',
            'sentences.*' => 'required|string|min:10|max:1000',
        ]);

        $sentences = $request->input('sentences');
        $postId = $request->input('post_id');

        $result = $analysisService->checkPlagiarism($sentences, $postId);
        return response()->json($result);
    }

    public function analyzeSeo(Request $request, \App\Services\ContentAnalysisService $analysisService)
    {
        $title = $request->input('title', '');
        $content = $request->input('content', '');
        
        $result = $analysisService->analyzeSeo($title, $content);
        return response()->json($result);
    }

    public function exportSample()
    {
        $filename = "posts-import-sample.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Title', 'TLDR', 'Excerpt', 'Content', 'FAQ', 'Tags', 'Category', 'Schedule Date', 'Status', 'Location']);
            fputcsv($file, [
                'Sample Post Title',
                'A quick summary of the sample post.',
                'This is a short excerpt.',
                '<p>This is the main HTML content of the post.</p>',
                '[{"q":"What is this?","a":"A sample FAQ."}]',
                'Tech, AI, Future',
                'Technology',
                now()->addDays(1)->format('Y-m-d H:i'),
                'draft',
                'New York, London'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export()
    {
        $posts = Post::with(['category', 'tags', 'locations'])->get();
        $filename = "posts-export-" . now()->format('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($posts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Title', 'TLDR', 'Excerpt', 'Content', 'FAQ', 'Tags', 'Category', 'Schedule Date', 'Status', 'Location']);

            foreach ($posts as $post) {
                fputcsv($file, [
                    $post->title,
                    $post->tldr,
                    $post->excerpt,
                    $post->content,
                    is_array($post->faqs) ? json_encode($post->faqs) : $post->faqs,
                    $post->tags->pluck('name')->implode(', '),
                    $post->category?->name ?? '',
                    $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d H:i') : '',
                    $post->status,
                    $post->locations->pluck('name')->implode(', ')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            return back()->with('error', 'CSV file is empty or invalid format.');
        }
        
        // Normalize headers
        $headers = array_map(function($h) {
            return strtolower(trim($h));
        }, $headerRow);

        $importedCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) continue;
            
            $data = [];
            foreach ($headers as $index => $colName) {
                $data[$colName] = $row[$index] ?? null;
            }

            $title = $data['title'] ?? '';
            $content = $data['content'] ?? '';
            if (empty($title) || empty($content)) continue;
            
            $slug = Str::slug($title);
            $excerpt = $data['excerpt'] ?? '';
            $tldr = $data['tldr'] ?? '';
            $status = $data['status'] ?? 'draft';
            $status = in_array(strtolower($status), ['draft', 'published', 'scheduled']) ? strtolower($status) : 'draft';
            
            $publishedAt = null;
            if (!empty($data['schedule date'])) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($data['schedule date']);
                } catch (\Exception $e) {
                    $publishedAt = null;
                }
            }

            $faqs = null;
            if (!empty($data['faq'])) {
                $decoded = json_decode($data['faq'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $faqs = $decoded;
                }
            }

            // Find or create category
            $categoryId = null;
            if (!empty($data['category'])) {
                $categoryName = trim(explode(',', $data['category'])[0]);
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName)]
                );
                $categoryId = $category->id;
            }

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'content' => $content,
                    'excerpt' => $excerpt,
                    'tldr' => $tldr,
                    'faqs' => $faqs,
                    'category_id' => $categoryId,
                    'status' => $status,
                    'published_at' => $publishedAt,
                    'author_id' => auth()->id(),
                    'reading_time' => Post::calculateReadingTime($content),
                ]
            );

            // Tags
            if (!empty($data['tags'])) {
                $tagNames = array_map('trim', explode(',', $data['tags']));
                $tagIds = [];
                foreach ($tagNames as $tName) {
                    if (empty($tName)) continue;
                    $tag = \App\Models\Tag::firstOrCreate(
                        ['name' => $tName],
                        ['slug' => Str::slug($tName)]
                    );
                    $tagIds[] = $tag->id;
                }
                $post->tags()->sync($tagIds);
            }

            // Locations
            if (!empty($data['location'])) {
                $locNames = array_map('trim', explode(',', $data['location']));
                $locIds = [];
                foreach ($locNames as $lName) {
                    if (empty($lName)) continue;
                    $loc = \App\Models\Location::firstOrCreate(
                        ['name' => $lName],
                        ['slug' => Str::slug($lName)]
                    );
                    $locIds[] = $loc->id;
                }
                $post->locations()->sync($locIds);
            }

            $importedCount++;
        }
        
        fclose($handle);
        return redirect()->route('admin.posts.index')->with('success', "Successfully imported or updated {$importedCount} posts.");
    }

    public function suggestFaqs(Request $request, \App\Services\ContentAnalysisService $analysisService)
    {
        $title = $request->input('title', '');
        $content = $request->input('content', '');
        
        try {
            $faqs = $analysisService->suggestFaqs($title, $content);
            return response()->json(['faqs' => $faqs]);
        } catch (\Exception $e) {
            Log::warning('FAQ generation failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
