<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class CategoryController extends Controller
{
    public function index()
    {
        // Stats
        $totalCategories = Category::count();
        $parentCategoriesCount = Category::whereNull('parent_id')->count();
        $subCategoriesCount = Category::whereNotNull('parent_id')->count();
        $emptyCategoriesCount = Category::doesntHave('posts')->count();

        // Major Categories Quick Links (Top 4 parent categories with most posts)
        $majorCategories = Category::whereNull('parent_id')
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(4)
            ->get();

        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        $query = Category::withCount('posts');

        if (!request()->has('filter') && !request()->has('sort') && !request()->has('search') && !request()->has('count_filter')) {
            $query->with('children')->whereNull('parent_id');
        }

        if (request()->has('filter')) {
            switch (request('filter')) {
                case 'top-level':
                    $query->whereNull('parent_id');
                    break;
                case 'subcategories':
                    $query->whereNotNull('parent_id');
                    break;
                case 'empty':
                    $query->having('posts_count', '=', 0);
                    break;
            }
        }

        if (request()->has('count_filter')) {
            switch (request('count_filter')) {
                case 'has_posts':
                    $query->having('posts_count', '>', 0);
                    break;
                case 'empty':
                    $query->having('posts_count', '=', 0);
                    break;
            }
        }

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (request()->has('sort')) {
            $dir = strtolower(request()->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            if (request()->sort === 'posts_count') {
                $query->orderBy('posts_count', $dir);
            } else {
                $query->orderBy(request()->sort, $dir);
            }
        } else {
            $query->orderBy('sort_order');
        }

        if (request()->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('admin.categories.index', compact(
            'categories', 
            'parentCategories',
            'totalCategories',
            'parentCategoriesCount',
            'subCategoriesCount',
            'emptyCategoriesCount',
            'majorCategories'
        ));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'color_code' => 'required|string|size:7',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color_code' => 'required|string|size:7',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);

        // Prevent setting a category as its own parent
        if ($validated['parent_id'] == $category->id) {
            $validated['parent_id'] = null;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete a category with sub-categories. Delete or move them first.');
        }

        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete a category that has related posts.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    public function autoHierarchy()
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        if (!$geminiKey) {
            return response()->json(['success' => false, 'message' => 'Gemini API key is not configured.'], 400);
        }

        $categories = Category::select('id', 'name', 'description')->get();
        if ($categories->count() < 2) {
            return response()->json(['success' => false, 'message' => 'Not enough categories to organize.'], 400);
        }

        $prompt = "You are a content organization AI. I will provide a list of categories with their IDs, names, and descriptions. " .
                  "Your task is to analyze them and determine logical parent-child relationships (hierarchy). " .
                  "Return ONLY a JSON object where the keys are the child category IDs, and the values are the parent category IDs. " .
                  "Do not nest deeper than 1 level (a category should either be a top-level parent or a child of a top-level parent). " .
                  "If a category should remain a top-level parent, omit it from the JSON or set its value to null. " .
                  "Example output: {\"2\": 1, \"5\": 3}\n\nCategories:\n";

        foreach ($categories as $cat) {
            $prompt .= "ID: {$cat->id} | Name: {$cat->name} | Desc: {$cat->description}\n";
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $geminiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                
                // Clean up markdown JSON block if present
                $text = preg_replace('/```json|```/', '', $text);
                $mapping = json_decode(trim($text), true);

                if (is_array($mapping)) {
                    // First, clear all parents to apply the new hierarchy fresh
                    Category::query()->update(['parent_id' => null]);

                    foreach ($mapping as $childId => $parentId) {
                        if ($childId != $parentId && $parentId !== null) {
                            Category::where('id', $childId)->update(['parent_id' => $parentId]);
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Categories organized successfully.']);
                } else {
                    \Log::error('AI Response failed to parse as array: ' . $text);
                }
            } else {
                \Log::error('Gemini API Error: ' . $response->body());
            }
            
            return response()->json(['success' => false, 'message' => 'Failed to parse AI response.']);

        } catch (\Exception $e) {
            Log::error('Auto Hierarchy Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during AI analysis.']);
        }
    }

    public function autoFill(Request $request)
    {
        $name = $request->input('name');
        if (!$name) {
            return response()->json(['error' => 'Category name is required'], 400);
        }

        $geminiKey = Setting::get('gemini_key', '');
        $anthropicKey = Setting::get('anthropic_key', '');
        $openAiKey = Setting::get('open_ai_key', '');

        if (empty($geminiKey) && empty($anthropicKey) && empty($openAiKey)) {
            return response()->json(['error' => 'No AI API key configured. Go to Settings > Integrations to add a Gemini, Anthropic, or OpenAI key.'], 400);
        }

        $prompt = "Generate metadata for a blog category named '{$name}'.\n" .
                  "Return ONLY a JSON object with the following keys:\n" .
                  "- 'slug': a URL-friendly slug (lowercase, hyphens).\n" .
                  "- 'description': a short, SEO-friendly description (1-2 sentences).\n" .
                  "- 'color_code': a hex color code that conceptually matches the category (e.g. green for nature, blue for tech).\n\n" .
                  "Example: {\"slug\": \"tech-news\", \"description\": \"Latest updates and insights in the world of technology.\", \"color_code\": \"#0d6efd\"}";

        $defaultModel = Setting::get('default_llm_model', 'auto');
        $providers = [];

        if (str_starts_with($defaultModel, 'gemini') && !empty($geminiKey)) {
            $providers[] = 'gemini';
        } elseif (str_starts_with($defaultModel, 'claude') && !empty($anthropicKey)) {
            $providers[] = 'anthropic';
        } elseif (str_starts_with($defaultModel, 'gpt') && !empty($openAiKey)) {
            $providers[] = 'openai';
        }

        if (!empty($geminiKey) && !in_array('gemini', $providers)) $providers[] = 'gemini';
        if (!empty($anthropicKey) && !in_array('anthropic', $providers)) $providers[] = 'anthropic';
        if (!empty($openAiKey) && !in_array('openai', $providers)) $providers[] = 'openai';

        $text = null;
        $errorMsg = '';
        $statusCode = 400;

        foreach ($providers as $provider) {
            try {
                if ($provider === 'gemini') {
                    $maxRetries = 3;
                    $attempt = 0;
                    while ($attempt < $maxRetries) {
                        $attempt++;
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $geminiKey, [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ],
                            'generationConfig' => [
                                'responseMimeType' => 'application/json',
                                'temperature' => 0.7,
                            ]
                        ]);

                        if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                            $response = $response->wait();
                        }

                        if ($response->status() === 503 || $response->status() === 429) {
                            if ($attempt < $maxRetries) {
                                sleep(2 * $attempt);
                                continue;
                            }
                        }
                        break;
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'Gemini API Error: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-fill: Gemini failed: {$err}");
                        $errorMsg = "Gemini: {$err}";
                        $statusCode = $response->status();
                    }
                } elseif ($provider === 'anthropic') {
                    $response = Http::timeout(60)
                        ->withHeaders([
                            'x-api-key' => $anthropicKey,
                            'anthropic-version' => '2023-06-01',
                            'content-type' => 'application/json',
                        ])
                        ->post('https://api.anthropic.com/v1/messages', [
                            'model' => 'claude-3-5-sonnet-20241022',
                            'max_tokens' => 300,
                            'messages' => [['role' => 'user', 'content' => $prompt]],
                            'temperature' => 0.7,
                        ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['content'][0]['text'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'Anthropic API Error: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-fill: Anthropic failed: {$err}");
                        $errorMsg = "Anthropic: {$err}";
                        $statusCode = $response->status();
                    }
                } elseif ($provider === 'openai') {
                    $response = Http::timeout(60)->withToken($openAiKey)->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'response_format' => ['type' => 'json_object'],
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'temperature' => 0.7,
                    ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['choices'][0]['message']['content'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'OpenAI API Error: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-fill: OpenAI failed: {$err}");
                        $errorMsg = "OpenAI: {$err}";
                        $statusCode = $response->status();
                    }
                }

                if (!empty($text)) {
                    if (preg_match('/\{.*\}/s', $text, $matches)) {
                        $jsonString = $matches[0];
                    } else {
                        $jsonString = preg_replace('/```json|```/', '', $text);
                    }

                    $mapping = json_decode(trim($jsonString), true);
                    if (is_array($mapping)) {
                        // Validate color code
                        if (isset($mapping['color_code'])) {
                            $colorCode = trim($mapping['color_code']);
                            if (!preg_match('/^#[0-9A-F]{6}$/i', $colorCode)) {
                                $mapping['color_code'] = '#2D7FF9';
                            }
                        }
                        return response()->json($mapping);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Category Auto-fill: Failed to parse AI response for {$provider}: " . substr($text, 0, 100));
                        $errorMsg = "{$provider}: Response failed to parse as JSON.";
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Category Auto-fill: {$provider} failed: " . $e->getMessage());
                $errorMsg = "{$provider}: Exception: " . $e->getMessage();
            }
        }

        return response()->json(['error' => 'All AI providers failed. Last error: ' . $errorMsg], $statusCode);
    }

    /**
     * Core AI generation and saving logic for category metadata (description, color_code).
     */
    public function autoFillCategory(Category $category): array
    {
        $geminiKey = Setting::get('gemini_key', '');
        $anthropicKey = Setting::get('anthropic_key', '');
        $openAiKey = Setting::get('open_ai_key', '');

        if (empty($geminiKey) && empty($anthropicKey) && empty($openAiKey)) {
            return ['error' => 'No AI API key configured. Go to Settings > Integrations to add a Gemini, Anthropic, or OpenAI key.'];
        }

        $parentName = $category->parent ? $category->parent->name : null;
        $prompt = "Generate metadata for a blog category named '{$category->name}'" . ($parentName ? " (which is a subcategory of '{$parentName}')" : "") . ".\n" .
                  "Return ONLY a JSON object with the following keys:\n" .
                  "- 'description': a short, SEO-friendly description (1-2 sentences) describing this category.\n" .
                  "- 'color_code': a hex color code that conceptually matches the category (e.g. green for nature, blue for tech).\n\n" .
                  "Example: {\"description\": \"Latest updates and insights in the world of technology.\", \"color_code\": \"#0d6efd\"}";

        $defaultModel = Setting::get('default_llm_model', 'auto');
        $providers = [];

        if (str_starts_with($defaultModel, 'gemini') && !empty($geminiKey)) {
            $providers[] = 'gemini';
        } elseif (str_starts_with($defaultModel, 'claude') && !empty($anthropicKey)) {
            $providers[] = 'anthropic';
        } elseif (str_starts_with($defaultModel, 'gpt') && !empty($openAiKey)) {
            $providers[] = 'openai';
        }

        if (!empty($geminiKey) && !in_array('gemini', $providers)) $providers[] = 'gemini';
        if (!empty($anthropicKey) && !in_array('anthropic', $providers)) $providers[] = 'anthropic';
        if (!empty($openAiKey) && !in_array('openai', $providers)) $providers[] = 'openai';

        $text = null;
        $errorMsg = '';

        foreach ($providers as $provider) {
            try {
                if ($provider === 'gemini') {
                    $maxRetries = 3;
                    $attempt = 0;
                    while ($attempt < $maxRetries) {
                        $attempt++;
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $geminiKey, [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ],
                            'generationConfig' => [
                                'responseMimeType' => 'application/json',
                                'temperature' => 0.7,
                            ]
                        ]);

                        if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                            $response = $response->wait();
                        }

                        if ($response->status() === 503 || $response->status() === 429) {
                            if ($attempt < $maxRetries) {
                                sleep(2 * $attempt);
                                continue;
                            }
                        }
                        break;
                    }

                    if ($response->successful()) {
                        $data = $response->json();
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'Gemini API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-Fill: Gemini failed: {$err}");
                        $errorMsg = "Gemini: {$err}";
                    }
                } elseif ($provider === 'anthropic') {
                    $response = Http::timeout(60)
                        ->withHeaders([
                            'x-api-key' => $anthropicKey,
                            'anthropic-version' => '2023-06-01',
                            'content-type' => 'application/json',
                        ])
                        ->post('https://api.anthropic.com/v1/messages', [
                            'model' => 'claude-3-5-sonnet-20241022',
                            'max_tokens' => 300,
                            'messages' => [['role' => 'user', 'content' => $prompt]],
                            'temperature' => 0.7,
                        ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['content'][0]['text'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'Anthropic API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-Fill: Anthropic failed: {$err}");
                        $errorMsg = "Anthropic: {$err}";
                    }
                } elseif ($provider === 'openai') {
                    $response = Http::timeout(60)->withToken($openAiKey)->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'response_format' => ['type' => 'json_object'],
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                        'temperature' => 0.7,
                    ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['choices'][0]['message']['content'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'OpenAI API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Category Auto-Fill: OpenAI failed: {$err}");
                        $errorMsg = "OpenAI: {$err}";
                    }
                }

                if (!empty($text)) {
                    if (preg_match('/\{.*\}/s', $text, $matches)) {
                        $jsonString = $matches[0];
                    } else {
                        $jsonString = preg_replace('/```json|```/', '', $text);
                    }

                    $mapping = json_decode(trim($jsonString), true);
                    if (is_array($mapping)) {
                        return $this->saveCategoryMetadata($category, $mapping);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Category Auto-Fill: Failed to parse AI response for {$provider}: " . substr($text, 0, 100));
                        $errorMsg = "{$provider}: Response failed to parse as JSON.";
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Category Auto-Fill: {$provider} failed: " . $e->getMessage());
                $errorMsg = "{$provider}: Exception: " . $e->getMessage();
            }
        }

        return ['error' => 'All AI providers failed. Last error: ' . $errorMsg];
    }

    public function saveCategoryMetadata(Category $category, array $data): array
    {
        $description = trim($data['description'] ?? '');
        $colorCode = trim($data['color_code'] ?? '#2D7FF9');

        // Validate color code
        if (!preg_match('/^#[0-9A-F]{6}$/i', $colorCode)) {
            $colorCode = '#2D7FF9';
        }

        $category->update([
            'description' => $description ?: $category->description,
            'color_code' => $colorCode ?: $category->color_code,
        ]);

        return [
            'success' => true,
            'description' => $category->description,
            'color_code' => $category->color_code,
        ];
    }

    public function autoFillSingle(Category $category)
    {
        $result = $this->autoFillCategory($category);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result);
    }
}
