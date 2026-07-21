<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Location;
use App\Models\Post;
use App\Models\PostMeta;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsAgentService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Run the automated news agent for a specific category.
     * Searches for breaking news, researches it, writes the article, resolves images,
     * makes a featured decision, and publishes it.
     */
    public function runForCategory(Category $category, ?string &$logOutput = null, bool $dryRun = false): ?Post
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value') ?: '';
        
        $logOutput .= "🌐 Running agent for category: [" . strtoupper($category->name) . "]\n";

        // Step 1: Discover breaking news / hot story of today
        $logOutput .= "🔎 Step 1: Searching for today's breaking news in {$category->name}...\n";
        $discovery = $this->discoverBreakingNews($category, $geminiKey, $logOutput);
        
        if (!$discovery || empty($discovery['search_query'])) {
            $msg = "⚠️ Step 1 failed: No breaking news discovered for {$category->name}.";
            Log::warning($msg);
            $logOutput .= $msg . "\n";
            return null;
        }

        $topic = $discovery['search_query'];
        $logOutput .= "💡 Discovered Topic: \"{$discovery['headline']}\"\n";
        $logOutput .= "   Search query: \"{$topic}\"\n";

        // Check if a post with a similar title/slug already exists to prevent duplicate publishing
        $slug = Str::slug($discovery['headline']);
        if (Post::withTrashed()->where('slug', $slug)->exists()) {
            $msg = "⏭️ Skipped: An article with a similar headline already exists in the database.";
            Log::info($msg);
            $logOutput .= $msg . "\n";
            return null;
        }

        // Step 2: Research and compose the grounded, detailed article
        $logOutput .= "📝 Step 2: Conducting deep research and writing the article (pinpoint accuracy)...\n";
        $articleData = $this->researchAndWriteArticle($category, $discovery, $geminiKey, $logOutput);
        if (!$articleData) {
            $msg = "⚠️ Step 2 failed: Could not generate grounded article content.";
            Log::warning($msg);
            $logOutput .= $msg . "\n";
            return null;
        }

        // Step 3: Featured decision making
        $isFeatured = $articleData['is_featured'] ?? false;
        $featuredDays = $articleData['featured_days'] ?? 0;
        $featuredReason = $articleData['featured_reason'] ?? 'Daily news.';
        $featuredUntil = $isFeatured ? now()->addDays($featuredDays) : null;

        $logOutput .= "🎯 Step 3: Featured Post Decision: " . ($isFeatured ? "✅ YES (Featured for {$featuredDays} days)" : "❌ NO") . "\n";
        $logOutput .= "   AI Reason: \"{$featuredReason}\"\n";

        if ($dryRun) {
            $logOutput .= "🧪 [DRY RUN] Content generated successfully. Post would be published.\n";
            return new Post([
                'title' => $articleData['headline'],
                'slug' => $slug,
                'content' => $articleData['content'],
                'excerpt' => $articleData['excerpt'],
                'category_id' => $category->id,
                'is_featured' => $isFeatured,
                'featured_until' => $featuredUntil,
            ]);
        }

        // Step 4: Resolve image (Internet search first, fallback to AI generation)
        $logOutput .= "🖼️ Step 4: Resolving featured image (Searching Wikipedia, fallback to Imagen/DALL-E/Pollinations)...\n";
        $imageResult = $this->imageService->resolveFeaturedImage(
            $topic,
            $articleData['image_prompt'] ?? $discovery['headline'],
            $slug
        );
        $imagePath = $imageResult['path'] ?? null;
        if ($imagePath) {
            $logOutput .= "   ✓ Image resolved successfully: storage/{$imagePath}\n";
        } else {
            $logOutput .= "   ⚠️ No image could be resolved, fallback to placeholder.\n";
        }

        // Default author: first superadmin, fallback to first user, fallback to 1
        $author = User::where('role', 'super_admin')->first() ?? User::first();
        $authorId = $author ? $author->id : 1;

        // Step 5: Publish the article
        $logOutput .= "🚀 Step 5: Publishing post to database...\n";
        $post = Post::create([
            'title'          => $articleData['headline'],
            'slug'           => $slug,
            'content'        => $articleData['content'],
            'excerpt'        => $articleData['excerpt'],
            'featured_image' => $imagePath,
            'category_id'    => $category->id,
            'author_id'      => $authorId,
            'status'         => 'published',
            'is_featured'    => $isFeatured,
            'featured_until' => $featuredUntil,
            'published_at'   => now(),
            'reading_time'   => Post::calculateReadingTime($articleData['content']),
        ]);

        // Create SEO Meta
        PostMeta::create([
            'post_id'          => $post->id,
            'meta_title'       => $articleData['meta_title'] ?? (mb_strlen($post->title) > 60 ? preg_replace('/\s+?(\S+)?$/', '', mb_substr($post->title, 0, 61)) : $post->title),
            'meta_description' => $articleData['meta_description'] ?? Str::limit($post->excerpt, 150),
            'schema_type'      => 'NewsArticle',
        ]);

        // Sync Tags
        if (!empty($articleData['tags'])) {
            $tagIds = collect($articleData['tags'])->map(function ($tagName) {
                $slug = Str::slug(trim($tagName));
                if (empty($slug)) return null;
                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => trim($tagName)]
                )->id;
            })->filter()->values();
            $post->tags()->sync($tagIds);
            $logOutput .= "   Synced tags: " . implode(', ', $articleData['tags']) . "\n";
        }

        // Sync Locations
        if (!empty($articleData['locations'])) {
            $post->locations()->sync($articleData['locations']);
            $locationNames = Location::whereIn('id', $articleData['locations'])->pluck('name')->toArray();
            $logOutput .= "   Synced locations: " . implode(', ', $locationNames) . "\n";
        }

        $logOutput .= "🎉 Done! Article successfully published: " . route('frontend.article', $post->slug) . "\n\n";

        return $post;
    }

    /**
     * Discover breaking news using Google News RSS feed and LLM selection.
     */
    private function discoverBreakingNews(Category $category, string $geminiKey, ?string &$logOutput): ?array
    {
        $currentDate = now()->format('F j, Y');
        
        $logOutput .= "   Fetching live Google News RSS feed...\n";
        $headlines = $this->getGoogleNewsHeadlines($category->slug);
        
        if (empty($headlines)) {
            $logOutput .= "   ❌ Failed to retrieve Google News RSS.\n";
            return null;
        }

        $logOutput .= "   Parsed top Google News headlines. Invoking model to select best breaking topic...\n";
        
        $headlinesString = "";
        foreach ($headlines as $index => $h) {
            $num = $index + 1;
            $headlinesString .= "{$num}. Headline: {$h['title']}\n   Summary details: {$h['description']}\n\n";
        }

        $prompt = "You are a senior editor at a global news agency. Today is {$currentDate}.\n" .
                  "Review this list of live Google News headlines for '{$category->name}':\n\n" .
                  $headlinesString . "\n" .
                  "Select the absolute most important, high-impact breaking news story from the list.\n" .
                  "Return a JSON object with these exact keys:\n" .
                  "- 'headline': Compelling polished headline (max 80 chars).\n" .
                  "- 'search_query': A 3-6 word search query for the specific event (e.g. 'Donald Trump Mount Rushmore speech' or 'Slain leader Khamenei funeral').\n" .
                  "- 'summary': A 1-2 sentence quick summary of what happened.\n" .
                  "- 'rationale': Why this is the top story.\n\n" .
                  "Return ONLY a valid JSON object. No markdown backticks, no other text.";

        return $this->callLlm($prompt, $geminiKey, $logOutput, 'search_query');
    }

    /**
     * Conduct research on the breaking topic and generate article details.
     */
    private function researchAndWriteArticle(Category $category, array $discovery, string $geminiKey, ?string &$logOutput): ?array
    {
        $currentDate = now()->format('F j, Y');
        $locations = Location::select('id', 'name')->get();
        $locationPrompt = "Available Locations in database (match these IDs if the news occurs in one of them, return as an array of IDs):\n";
        foreach ($locations as $loc) {
            $locationPrompt .= "{$loc->id} - {$loc->name}\n";
        }

        $prompt = "You are a professional journalist for Atomni, a premium digital news platform. Today is {$currentDate}.\n" .
                  "Write a detailed, high-engaging, factually-accurate news article based on this breaking story:\n" .
                  "Headline: \"{$discovery['headline']}\"\n" .
                  "Summary context: \"{$discovery['summary']}\"\n" .
                  "Event details: \"{$discovery['rationale']}\"\n\n" .
                  "INSTRUCTIONS:\n" .
                  "1. Write a 300-500 word article in professional journalism style, using clean HTML paragraphs (<p>, <h2>, etc.). Do NOT write AI clichés.\n" .
                  "2. Assign 3-5 relevant, multi-word topical tags (e.g. 'Electric Vehicles', 'Open Source Software'). Do not use fragments or hashtags.\n" .
                  "3. Determine if this news is a major breaking event (high global impact, political shift, big disaster, historic announcement). Set 'is_featured' to true and specify 'featured_days' (1 to 7) if it is highly important, otherwise false.\n" .
                  "4. Write an image prompt describing a vivid photorealistic editorial photo of the event.\n" .
                  "5. {$locationPrompt}\n\n" .
                  "Return ONLY a valid JSON object with these keys:\n" .
                  "- 'headline': The final polished headline (max 90 chars)\n" .
                  "- 'content': The complete article content in clean HTML paragraphs (<p>, <h2>, etc.)\n" .
                  "- 'excerpt': A 1-2 sentence engaging summary (max 200 chars)\n" .
                  "- 'is_featured': boolean\n" .
                  "- 'featured_days': integer (0 if is_featured is false, 1-7 if true)\n" .
                  "- 'featured_reason': string explaining the featured decision\n" .
                  "- 'tags': array of tag strings\n" .
                  "- 'locations': array of location IDs (must match available location IDs or empty array)\n" .
                  "- 'meta_title': SEO page title (max 60 chars)\n" .
                  "- 'meta_description': SEO meta description (max 160 chars)\n" .
                  "- 'image_prompt': Vivid image prompt for generator\n\n" .
                  "Return ONLY a valid JSON object. No markdown backticks, no other text.";

        return $this->callLlm($prompt, $geminiKey, $logOutput, 'content');
    }

    /**
     * Unified LLM client with Gemini and Pollinations.ai waterfall.
     */
    private function callLlm(string $prompt, string $geminiKey, ?string &$logOutput, string $requiredKey): ?array
    {
        // 1. Try Gemini (gemini-flash-latest) without search grounding (which is blocked/rate-limited on free keys)
        if (!empty($geminiKey)) {
            try {
                $model = 'gemini-flash-latest';
                $response = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'temperature' => 0.5,
                        ]
                    ]
                );

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    $text = preg_replace('/```json|```/', '', $text);
                    $data = json_decode(trim($text), true);
                    if (is_array($data) && isset($data[$requiredKey])) {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                // Silent fallback
            }
        }

        // 2. Fallback to keyless Pollinations.ai with model waterfall (openai -> mistral -> llama) and 429 retry
        $models = ['openai', 'mistral', 'llama'];
        foreach ($models as $m) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout(45)->post(
                        'https://text.pollinations.ai/',
                        [
                            'messages' => [['role' => 'user', 'content' => $prompt]],
                            'model' => $m,
                            'jsonMode' => true,
                        ]
                    );

                    if ($response->status() == 429) {
                        $logOutput .= "   ⏳ Model [{$m}] rate-limited (429). Retrying in 5 seconds...\n";
                        sleep(5);
                        continue;
                    }

                    if ($response->successful()) {
                        $text = $response->body();
                        
                        // Strip markdown fences if present
                        $text = trim($text);
                        if (str_starts_with($text, '```')) {
                            $text = preg_replace('/^```(?:json)?/i', '', $text);
                            $text = preg_replace('/```$/', '', $text);
                            $text = trim($text);
                        }

                        $data = json_decode($text, true);
                        if (is_array($data) && isset($data[$requiredKey])) {
                            return $data;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("NewsAgentService: Pollinations.ai Model [{$m}] failed: " . $e->getMessage());
                }
                break; // Break the attempt loop if not 429
            }
        }

        return null;
    }

    /**
     * Fetch Google News RSS for a specific category using short section topics.
     */
    private function getGoogleNewsHeadlines(string $categorySlug): ?array
    {
        $topicMap = [
            'world' => 'WORLD',
            'politics' => 'NATION',
            'technology' => 'TECHNOLOGY',
            'business' => 'BUSINESS',
            'science' => 'SCIENCE',
            'sports' => 'SPORTS',
            'entertainment' => 'ENTERTAINMENT',
            'health' => 'HEALTH',
        ];

        $topic = $topicMap[strtolower($categorySlug)] ?? 'WORLD';
        $url = "https://news.google.com/rss/headlines/section/topic/{$topic}?hl=en-US&gl=US&ceid=US:en";

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ])
                ->get($url);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if ($xml && isset($xml->channel->item)) {
                    $items = [];
                    foreach ($xml->channel->item as $item) {
                        $items[] = [
                            'title' => (string)$item->title,
                            'link' => (string)$item->link,
                            'description' => strip_tags((string)$item->description),
                        ];
                        if (count($items) >= 5) break;
                    }
                    return $items;
                }
            }
        } catch (\Exception $e) {
            Log::warning("NewsAgentService: Google News RSS fetch failed: " . $e->getMessage());
        }

        return null;
    }
}
