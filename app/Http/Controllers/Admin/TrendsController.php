<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostMeta;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrendsController extends Controller
{
    /**
     * Generate trending topic suggestions via AI.
     */
    public function generate(Request $request)
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        $openAiKey = Setting::where('key', 'open_ai_key')->value('value');
        $anthropicKey = Setting::where('key', 'anthropic_key')->value('value');

        if (empty($geminiKey) && empty($openAiKey) && empty($anthropicKey)) {
            return response()->json([
                'error' => 'No AI API key configured. Go to Settings > Integrations to add a Gemini, OpenAI, or Anthropic key.'
            ], 422);
        }

        $rssUrl = 'https://trends.google.com/trends/trendingsearches/daily/rss?geo=US';
        $trendingTerms = [];
        try {
            $rssResponse = Http::timeout(10)->get($rssUrl);
            if ($rssResponse->successful()) {
                $xml = simplexml_load_string($rssResponse->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if ($xml && isset($xml->channel->item)) {
                    $count = 0;
                    foreach ($xml->channel->item as $item) {
                        $trendingTerms[] = (string)$item->title;
                        $count++;
                        if ($count >= 5) break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch Google Trends RSS: " . $e->getMessage());
        }

        $trendingContext = empty($trendingTerms) 
            ? "Generate exactly 5 trending news topic suggestions that have high viral/traffic potential right now." 
            : "The current top trending searches on Google are: " . implode(', ', $trendingTerms) . ".\n\nGenerate exactly 5 news topic suggestions based directly on these real-world trending topics.";

        $categories = Category::pluck('name')->implode(', ');

        $prompt = <<<PROMPT
You are a senior news editor and SEO strategist for a digital news platform called Atomni. 
The platform has these categories: {$categories}.

{$trendingContext}

CONTENT TONE GUIDELINES:
- Write like a real journalist, NOT an AI. Use natural, conversational language.
- Vary sentence lengths. Mix short punchy sentences with longer ones.
- Avoid AI clichés like "delve into", "it's important to note", "in today's world", "landscape", "paradigm shift".
- Include specific details, data points, or quotes where relevant.
- Write with personality and authority. Be direct, not passive.
- Use active voice. Start with strong verbs. Hook the reader immediately.

For each topic, return a JSON object with these exact keys:
- "headline": A compelling, click-worthy yet honest news headline (max 80 chars)
- "category": The best-fit category from the list above (must match exactly)
- "excerpt": A 1-2 sentence hook/summary (max 200 chars)
- "content": A full 300-500 word draft article body in clean HTML paragraphs (<p> tags only). Must sound human-written.
- "tags": An array of 3-5 relevant tag strings
- "meta_title": SEO-optimized page title (max 60 chars)
- "meta_description": SEO meta description (max 160 chars)
- "image_prompt": A vivid description of what the featured image should depict (for AI image generation)
- "seo_score": Integer 0-100 rating how SEO-optimized the headline, meta, and content structure are
- "geo_score": Integer 0-100 rating the geographic trending potential and regional relevance
- "authenticity_score": Integer 0-100 rating how original, factual, and trustworthy the content is
- "plagiarism_risk": Integer 0-100 estimating the likelihood this content overlaps with existing articles (lower is better)

Return ONLY a valid JSON array of 5 objects. No markdown, no code fences, no explanation text.
PROMPT;

        try {
            $text = null;
            $modelUsed = '';
            $providers = [];

            $defaultModel = Setting::where('key', 'default_llm_model')->value('value') ?? 'auto';

            // Build ordered list of available providers based on default model
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

            // Waterfall: try each provider until one succeeds
            foreach ($providers as $provider) {
                try {
                    if ($provider === 'gemini') {
                        $modelUsed = 'Gemini 2.5 Flash';
                        $maxRetries = 3;
                        $attempt = 0;
                        while ($attempt < $maxRetries) {
                            $attempt++;
                            /** @var \Illuminate\Http\Client\Response $response */
                            $response = Http::timeout(90)->post(
                                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}",
                                [
                                    'contents' => [['parts' => [['text' => $prompt]]]],
                                    'generationConfig' => [
                                        'responseMimeType' => 'application/json',
                                        'temperature' => 0.9,
                                    ],
                                ]
                            );
                            
                            if ($response->status() === 503 || $response->status() === 429) {
                                if ($attempt < $maxRetries) {
                                    sleep(2 * $attempt); // wait 2s, then 4s before retrying
                                    continue;
                                }
                            }

                            $body = $response->json();
                            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                            break;
                        }

                        // If blocked or empty, log and try next
                        if (empty($text)) {
                            Log::warning("Trends: Gemini returned empty. Status: {$response->status()}, Body: " . substr($response->body(), 0, 500));
                            continue;
                        }

                    } elseif ($provider === 'anthropic') {
                        $modelUsed = 'Claude 3.5 Sonnet';
                        /** @var \Illuminate\Http\Client\Response $response */
                        $response = Http::timeout(90)
                            ->withHeaders([
                                'x-api-key' => $anthropicKey,
                                'anthropic-version' => '2023-06-01',
                                'content-type' => 'application/json',
                            ])
                            ->post('https://api.anthropic.com/v1/messages', [
                                'model' => 'claude-3-5-sonnet-20241022',
                                'max_tokens' => 4096,
                                'messages' => [['role' => 'user', 'content' => $prompt]],
                                'temperature' => 0.9,
                            ]);
                        $body = $response->json();
                        $text = $body['content'][0]['text'] ?? null;

                        if (empty($text)) {
                            Log::warning("Trends: Anthropic returned empty. Status: {$response->status()}, Body: " . substr($response->body(), 0, 500));
                            continue;
                        }

                    } elseif ($provider === 'openai') {
                        $modelUsed = 'GPT-4o';
                        /** @var \Illuminate\Http\Client\Response $response */
                        $response = Http::timeout(90)->withToken($openAiKey)->post(
                            'https://api.openai.com/v1/chat/completions',
                            [
                                'model' => 'gpt-4o',
                                'messages' => [['role' => 'user', 'content' => $prompt]],
                                'temperature' => 0.9,
                                'response_format' => ['type' => 'json_object'],
                            ]
                        );
                        $body = $response->json();
                        $text = $body['choices'][0]['message']['content'] ?? null;

                        if (empty($text)) {
                            Log::warning("Trends: OpenAI returned empty. Status: {$response->status()}, Body: " . substr($response->body(), 0, 500));
                            continue;
                        }
                    }

                    // If we got text, break out of the loop
                    if (!empty($text)) break;

                } catch (\Exception $providerError) {
                    Log::warning("Trends: {$provider} failed: " . $providerError->getMessage());
                    continue; // Try next provider
                }
            }

            if (empty($text)) {
                return response()->json(['error' => 'AI returned an empty response. Please try again.'], 500);
            }

            // Parse JSON — handle both array and object-with-array structures
            $topics = json_decode($text, true);

            if (!is_array($topics)) {
                return response()->json(['error' => 'Failed to parse AI response. Please try again.'], 500);
            }

            // If OpenAI wraps it in a keyed object, extract the array
            if (isset($topics[0])) {
                // Already an indexed array
            } else {
                // Probably wrapped like { "topics": [...] }
                $topics = reset($topics);
                if (!is_array($topics) || !isset($topics[0])) {
                    return response()->json(['error' => 'Unexpected AI response format. Please try again.'], 500);
                }
            }

            return response()->json(['topics' => $topics]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'AI request failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Publish one AI-suggested topic as a live post.
     */
    public function publish(Request $request)
    {
        $validated = $request->validate([
            'headline'         => 'required|string|max:255',
            'category'         => 'required|string|max:100',
            'excerpt'          => 'required|string|max:500',
            'content'          => 'required|string',
            'tags'             => 'required|array',
            'meta_title'       => 'required|string|max:70',
            'meta_description' => 'required|string|max:200',
            'image_prompt'     => 'nullable|string|max:500',
            'image_mode'       => 'required|string|in:ai,manual,skip',
            'manual_uploads.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240', // 10MB max per file
        ]);

        // ── Handle Featured Image ────────────────────────────────
        $featuredImagePath = null;
        $mediaRecord = null;

        if ($validated['image_mode'] === 'ai' && !empty($validated['image_prompt'])) {
            $result = $this->generateFeaturedImage(
                $validated['image_prompt'],
                Str::slug($validated['headline'])
            );
            $featuredImagePath = $result['path'] ?? null;
            $mediaRecord = $result['media'] ?? null;
        } elseif ($validated['image_mode'] === 'manual' && $request->hasFile('manual_uploads')) {
            $files = $request->file('manual_uploads');
            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '-' . $index . '.' . $extension;
                    $path = $file->storeAs('images', $fileName, 'public');
                    
                    // Create Media record for every uploaded file
                    $newMedia = \App\Models\Media::create([
                        'file_name' => $fileName,
                        'file_path' => 'images/' . $fileName,
                        'mime_type' => $file->getMimeType(),
                        'size_kb'   => round($file->getSize() / 1024, 2),
                    ]);

                    // Use the first uploaded file as the featured image
                    if ($index === 0) {
                        $featuredImagePath = 'images/' . $fileName;
                        $mediaRecord = $newMedia;
                    }
                }
            }
        }

        // Resolve or create category
        $category = Category::firstOrCreate(
            ['name' => $validated['category']],
            ['slug' => Str::slug($validated['category'])]
        );

        // Create the post
        $post = Post::create([
            'title'          => $validated['headline'],
            'slug'           => Str::slug($validated['headline']),
            'content'        => $validated['content'],
            'excerpt'        => $validated['excerpt'],
            'featured_image' => $featuredImagePath,
            'category_id'    => $category->id,
            'author_id'      => auth()->id(),
            'status'         => 'published',
            'published_at'   => now(),
            'reading_time'   => Post::calculateReadingTime($validated['content']),
        ]);

        // Create SEO meta
        PostMeta::create([
            'post_id'          => $post->id,
            'meta_title'       => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'schema_type'      => 'NewsArticle',
        ]);

        // Sync tags
        $tagIds = collect($validated['tags'])->map(function ($tagName) {
            $slug = Str::slug(trim($tagName));
            if (empty($slug)) return null;
            return Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => trim($tagName)]
            )->id;
        })->filter()->values();
        $post->tags()->sync($tagIds);

        return response()->json([
            'success' => true,
            'message' => "'{$post->title}' has been published!",
            'post_url' => route('frontend.article', $post->slug),
            'admin_url' => route('admin.posts.edit', $post),
            'featured_image_url' => $featuredImagePath ? asset('storage/' . $featuredImagePath) : null,
            'image_mode' => $validated['image_mode'],
        ]);
    }

    /**
     * Generate a featured image using multiple AI providers with automatic fallback.
     *
     * Waterfall order:
     *  1. Gemini Flash Image Generation  — FREE with any Gemini API key (no billing needed)
     *  2. Gemini Imagen 3 Fast           — Requires Google Cloud billing
     *  3. Gemini Imagen 3                — Requires Google Cloud billing
     *  4. OpenAI DALL-E 3               — Paid, requires OpenAI key
     *  5. Pollinations.ai               — 100% FREE, no API key required (final safety net)
     */
    private function generateFeaturedImage(string $imagePrompt, string $slug): array
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        $openAiKey = Setting::where('key', 'open_ai_key')->value('value');

        $fullPrompt = "Professional news article featured image: {$imagePrompt}. Photorealistic, high quality, editorial style, 16:9 aspect ratio, no text overlays.";

        // ── 1. Gemini Flash Image Generation (FREE tier — no billing needed) ────
        if (!empty($geminiKey)) {
            try {
                $response = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-preview-image-generation:generateContent?key={$geminiKey}",
                    [
                        'contents' => [
                            ['parts' => [['text' => $fullPrompt]]]
                        ],
                        'generationConfig' => [
                            'responseModalities' => ['IMAGE', 'TEXT'],
                        ],
                    ]
                );

                $parts = $response->json('candidates.0.content.parts') ?? [];
                foreach ($parts as $part) {
                    if (!empty($part['inlineData']['data'])) {
                        $imageBytes = base64_decode($part['inlineData']['data']);
                        $ext = str_contains($part['inlineData']['mimeType'] ?? '', 'jpeg') ? 'jpg' : 'png';
                        Log::info("AI Image: Generated via Gemini Flash (free) for [{$slug}]");
                        return $this->saveGeneratedImage($imageBytes, $slug, $imagePrompt, $ext);
                    }
                }

                Log::warning('Gemini Flash Image Gen: No image in response. Status: ' . $response->status() . ', Body: ' . substr($response->body(), 0, 400));

            } catch (\Exception $e) {
                Log::warning('Gemini Flash Image Gen failed: ' . $e->getMessage());
            }
        }

        // ── 2 & 3. Gemini Imagen 3 Fast / Imagen 3 (requires billing) ────────
        if (!empty($geminiKey)) {
            foreach (['imagen-3.0-fast-generate-001', 'imagen-3.0-generate-002'] as $imagenModel) {
                try {
                    $response = Http::timeout(60)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$imagenModel}:predict?key={$geminiKey}",
                        [
                            'instances'  => [['prompt' => $fullPrompt]],
                            'parameters' => ['sampleCount' => 1, 'aspectRatio' => '16:9'],
                        ]
                    );

                    $imageData = $response->json('predictions.0.bytesBase64Encoded');

                    if (!empty($imageData)) {
                        Log::info("AI Image: Generated via {$imagenModel} for [{$slug}]");
                        return $this->saveGeneratedImage(base64_decode($imageData), $slug, $imagePrompt, 'png');
                    }

                    Log::warning("Imagen [{$imagenModel}]: Empty. Status: {$response->status()}, Body: " . substr($response->body(), 0, 300));

                } catch (\Exception $e) {
                    Log::warning("Imagen [{$imagenModel}] failed: " . $e->getMessage());
                }
            }
        }

        // ── 4. OpenAI DALL-E 3 (paid) ────────────────────────────────────────
        if (!empty($openAiKey)) {
            try {
                $response = Http::timeout(90)->withToken($openAiKey)->post(
                    'https://api.openai.com/v1/images/generations',
                    [
                        'model'   => 'dall-e-3',
                        'prompt'  => $fullPrompt,
                        'n'       => 1,
                        'size'    => '1792x1024',
                        'quality' => 'standard',
                    ]
                );

                $imageUrl = $response->json('data.0.url');

                if (!empty($imageUrl)) {
                    $imageBytes = file_get_contents($imageUrl);
                    if ($imageBytes !== false) {
                        Log::info("AI Image: Generated via DALL-E 3 for [{$slug}]");
                        return $this->saveGeneratedImage($imageBytes, $slug, $imagePrompt, 'png');
                    }
                }

                Log::warning('DALL-E 3: Empty response. Status: ' . $response->status() . ', Body: ' . substr($response->body(), 0, 300));

            } catch (\Exception $e) {
                Log::warning('DALL-E 3 image generation failed: ' . $e->getMessage());
            }
        }

        // ── 5. Pollinations.ai — 100% FREE, no API key needed ────────────────
        try {
            // Truncate prompt to fit URL limits and strip special chars
            $safePrompt   = urlencode(substr($fullPrompt, 0, 400));
            $pollinateUrl = "https://image.pollinations.ai/prompt/{$safePrompt}?width=1344&height=768&model=flux&nologo=true&enhance=true&private=true";

            $ctx = stream_context_create([
                'http' => [
                    'timeout'       => 60,
                    'follow_location' => 1,
                    'user_agent'    => 'AtomniCMS/1.0',
                ],
                'ssl' => ['verify_peer' => false],
            ]);

            $imageBytes = @file_get_contents($pollinateUrl, false, $ctx);

            if ($imageBytes !== false && strlen($imageBytes) > 1000) {
                Log::info("AI Image: Generated via Pollinations.ai (free) for [{$slug}]");
                return $this->saveGeneratedImage($imageBytes, $slug, $imagePrompt, 'jpg');
            }

            Log::warning("Pollinations.ai: Failed or returned empty body for [{$slug}]");

        } catch (\Exception $e) {
            Log::warning('Pollinations.ai image generation failed: ' . $e->getMessage());
        }

        // All providers exhausted
        Log::warning("generateFeaturedImage: All providers failed for slug [{$slug}].");
        return ['path' => null, 'media' => null];
    }

    /**
     * Save raw image bytes to disk, apply watermark, and create a Media library record.
     */
    private function saveGeneratedImage(string $imageBytes, string $slug, string $imagePrompt, string $ext): array
    {
        try {
            $filename = "images/ai-{$slug}-" . time() . '.' . $ext;
            $fullPath = public_path("storage/{$filename}");

            // Ensure directory exists
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $imageBytes);
            $this->addWatermark($fullPath);

            $imageInfo = @getimagesize($fullPath);
            $width     = $imageInfo[0] ?? 1200;
            $height    = $imageInfo[1] ?? 675;
            $sizeKb    = (int) ceil(filesize($fullPath) / 1024);
            $mimeType  = $ext === 'png' ? 'image/png' : 'image/jpeg';

            $media = \App\Models\Media::create([
                'user_id'   => auth()->id(),
                'file_path' => $filename,
                'file_name' => basename($filename),
                'alt_text'  => Str::limit($imagePrompt, 125),
                'mime_type' => $mimeType,
                'size_kb'   => $sizeKb,
                'width'     => $width,
                'height'    => $height,
            ]);

            return ['path' => $filename, 'media' => $media];

        } catch (\Exception $e) {
            Log::warning('saveGeneratedImage failed: ' . $e->getMessage());
            return ['path' => null, 'media' => null];
        }
    }

    /**
     * Add a subtle Atomni watermark to the bottom-right corner of an image.
     */
    private function addWatermark(string $imagePath): void
    {
        try {
            $image = @imagecreatefrompng($imagePath);
            if (!$image) return;

            $width = imagesx($image);
            $height = imagesy($image);

            // Create semi-transparent white text watermark
            $fontSize = max(12, (int)($width * 0.018));  // ~2% of width
            $padding = (int)($width * 0.02);

            // Use a built-in font for simplicity (GD built-in)
            $textColor = imagecolorallocatealpha($image, 255, 255, 255, 60); // white, ~75% transparent
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 80);     // black shadow

            $watermarkText = 'ATOMNI';

            // Try to use a TrueType font if available, otherwise use built-in
            $fontPath = public_path('fonts/Inter-Bold.ttf');
            if (file_exists($fontPath) && function_exists('imagettftext')) {
                // TTF version — nicer
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $watermarkText);
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                $x = $width - $textWidth - $padding;
                $y = $height - $padding;

                // Shadow
                imagettftext($image, $fontSize, 0, $x + 1, $y + 1, $shadowColor, $fontPath, $watermarkText);
                // Text
                imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $watermarkText);
            } else {
                // Fallback: built-in GD font
                $font = 5; // largest built-in font
                $charWidth = imagefontwidth($font);
                $charHeight = imagefontheight($font);
                $textWidth = strlen($watermarkText) * $charWidth;
                $x = $width - $textWidth - $padding;
                $y = $height - $charHeight - $padding;

                // Shadow
                imagestring($image, $font, $x + 1, $y + 1, $watermarkText, $shadowColor);
                // Text
                imagestring($image, $font, $x, $y, $watermarkText, $textColor);
            }

            // Save
            imagepng($image, $imagePath, 6);
            imagedestroy($image);

        } catch (\Exception $e) {
            Log::warning('Watermark failed: ' . $e->getMessage());
        }
    }
}
