<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Resolve a featured image for a post by searching the internet first,
     * and falling back to AI image generation.
     */
    public function resolveFeaturedImage(string $searchQuery, string $imagePrompt, string $slug, ?int $userId = null): array
    {
        // 1. Try to search the internet (Wikipedia/Wikimedia Commons)
        $internetImage = $this->searchInternetImage($searchQuery);
        if ($internetImage) {
            $saved = $this->saveImageFromUrl($internetImage, $slug, $searchQuery, $userId);
            if ($saved['path']) {
                Log::info("ImageService: Resolved internet image for [{$slug}] via Wikipedia.");
                return $saved;
            }
        }

        // 2. Fall back to AI Image Generation
        Log::info("ImageService: Internet search returned no images. Falling back to AI generation for [{$slug}].");
        return $this->generateFeaturedImage($imagePrompt, $slug, $userId);
    }

    /**
     * Search Wikipedia API for a relevant public domain / creative commons image.
     */
    public function searchInternetImage(string $query): ?string
    {
        if (empty(trim($query))) {
            return null;
        }

        try {
            // Clean up query for Wikipedia search
            $cleanQuery = urlencode(strip_tags(trim($query)));
            $url = "https://en.wikipedia.org/w/api.php?action=query&format=json&prop=pageimages&generator=search&gsrsearch={$cleanQuery}&gsrlimit=3&piprop=original|thumbnail&pithumbsize=1200";

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $pages = $data['query']['pages'] ?? [];

                foreach ($pages as $page) {
                    // Try to get original high-res image first
                    $imageUrl = $page['original']['source'] ?? $page['thumbnail']['source'] ?? null;
                    if ($imageUrl && str_starts_with($imageUrl, 'http')) {
                        // Skip icons or very small flags
                        $ext = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            return $imageUrl;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("ImageService: Wikipedia search failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Download and save an image from a direct URL.
     */
    private function saveImageFromUrl(string $url, string $slug, string $altText, ?int $userId = null): array
    {
        try {
            $response = Http::timeout(20)->get($url);
            if ($response->successful()) {
                $imageBytes = $response->body();
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                return $this->saveRawImage($imageBytes, $slug, $altText, $ext, $userId);
            }
        } catch (\Exception $e) {
            Log::warning("ImageService: Failed to download image from {$url}: " . $e->getMessage());
        }

        return ['path' => null, 'media' => null];
    }

    /**
     * Generate featured image using waterfall AI image providers.
     */
    public function generateFeaturedImage(string $imagePrompt, string $slug, ?int $userId = null): array
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        $openAiKey = Setting::where('key', 'open_ai_key')->value('value');

        $fullPrompt = "Professional news article featured image: {$imagePrompt}. Photorealistic, high quality, editorial style, 16:9 aspect ratio, no text overlays.";

        // ── 1. Gemini Flash Image Generation (FREE tier) ────
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
                        Log::info("ImageService: Generated via Gemini Flash (free) for [{$slug}]");
                        return $this->saveRawImage($imageBytes, $slug, $imagePrompt, $ext, $userId);
                    }
                }
                Log::warning('ImageService: Gemini Flash Image Gen empty. Status: ' . $response->status());
            } catch (\Exception $e) {
                Log::warning('ImageService: Gemini Flash Image Gen failed: ' . $e->getMessage());
            }
        }

        // ── 2. Gemini Imagen 3 models ────
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
                        Log::info("ImageService: Generated via {$imagenModel} for [{$slug}]");
                        return $this->saveRawImage(base64_decode($imageData), $slug, $imagePrompt, 'png', $userId);
                    }
                } catch (\Exception $e) {
                    Log::warning("ImageService: {$imagenModel} failed: " . $e->getMessage());
                }
            }
        }

        // ── 3. OpenAI DALL-E 3 (paid) ────
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
                        Log::info("ImageService: Generated via DALL-E 3 for [{$slug}]");
                        return $this->saveRawImage($imageBytes, $slug, $imagePrompt, 'png', $userId);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('ImageService: DALL-E 3 failed: ' . $e->getMessage());
            }
        }

        // ── 4. Pollinations.ai (100% FREE, final safety net) ────
        try {
            $safePrompt = urlencode(substr($fullPrompt, 0, 400));
            $pollinateUrl = "https://image.pollinations.ai/prompt/{$safePrompt}?width=1344&height=768&model=flux&nologo=true&enhance=true&private=true";

            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'follow_location' => 1,
                    'user_agent' => 'AtomniCMS/1.0',
                ],
                'ssl' => ['verify_peer' => false],
            ]);

            $imageBytes = @file_get_contents($pollinateUrl, false, $ctx);
            if ($imageBytes !== false && strlen($imageBytes) > 1000) {
                Log::info("ImageService: Generated via Pollinations.ai for [{$slug}]");
                return $this->saveRawImage($imageBytes, $slug, $imagePrompt, 'jpg', $userId);
            }
        } catch (\Exception $e) {
            Log::warning('ImageService: Pollinations.ai failed: ' . $e->getMessage());
        }

        Log::warning("ImageService: All image resolving strategies failed for [{$slug}].");
        return ['path' => null, 'media' => null];
    }

    /**
     * Save raw image bytes to disk, apply watermarks, and register it in the Media library.
     */
    public function saveRawImage(string $imageBytes, string $slug, string $imagePrompt, string $ext, ?int $userId = null): array
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

            $media = Media::create([
                'user_id'   => $userId ?: (auth()->id() ?: 1),
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
            Log::warning('ImageService saveRawImage failed: ' . $e->getMessage());
            return ['path' => null, 'media' => null];
        }
    }

    /**
     * Add a subtle Atomni watermark to the bottom-right corner of an image.
     */
     private function addWatermark(string $imagePath): void
     {
         if (!extension_loaded('gd') || !function_exists('imagecreatefromjpeg')) {
             Log::info("ImageService: GD extension not loaded. Skipping watermark.");
             return;
         }

         try {
            $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            if ($ext === 'png') {
                $image = @imagecreatefrompng($imagePath);
            } else {
                $image = @imagecreatefromjpeg($imagePath);
            }
            
            if (!$image) return;

            $width = imagesx($image);
            $height = imagesy($image);

            // Create semi-transparent white text watermark
            $fontSize = max(12, (int)($width * 0.018));  // ~2% of width
            $padding = (int)($width * 0.02);

            $textColor = imagecolorallocatealpha($image, 255, 255, 255, 60); // white, ~75% transparent
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 80);     // black shadow

            $watermarkText = 'ATOMNI';

            // Try to use a TrueType font if available, otherwise use GD built-in
            $fontPath = public_path('fonts/Inter-Bold.ttf');
            if (file_exists($fontPath) && function_exists('imagettftext')) {
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $watermarkText);
                $textWidth = $bbox[2] - $bbox[0];
                $x = $width - $textWidth - $padding;
                $y = $height - $padding;

                // Shadow & Text
                imagettftext($image, $fontSize, 0, $x + 1, $y + 1, $shadowColor, $fontPath, $watermarkText);
                imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $watermarkText);
            } else {
                $font = 5; // GD built-in font
                $charWidth = imagefontwidth($font);
                $charHeight = imagefontheight($font);
                $textWidth = strlen($watermarkText) * $charWidth;
                $x = $width - $textWidth - $padding;
                $y = $height - $charHeight - $padding;

                // Shadow & Text
                imagestring($image, $font, $x + 1, $y + 1, $watermarkText, $shadowColor);
                imagestring($image, $font, $x, $y, $watermarkText, $textColor);
            }

            // Save back
            if ($ext === 'png') {
                imagepng($image, $imagePath, 6);
            } else {
                imagejpeg($image, $imagePath, 85);
            }
            imagedestroy($image);

        } catch (\Exception $e) {
            Log::warning('ImageService watermark failed: ' . $e->getMessage());
        }
    }
}
