<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use App\Models\Media;

class AiController extends Controller
{
    public function generateAltText(Request $request, Media $medium)
    {
        $result = $this->autoFillMedia($medium);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result);
    }

    /**
     * Core AI generation and saving logic for media metadata (alt text, file name).
     */
    public function autoFillMedia(Media $medium): array
    {
        $geminiKey = Setting::get('gemini_key', '');
        $anthropicKey = Setting::get('anthropic_key', '');
        $openAiKey = Setting::get('open_ai_key', '');

        if (empty($geminiKey) && empty($anthropicKey) && empty($openAiKey)) {
            return ['error' => 'No AI API key configured. Go to Settings > Integrations to add a Gemini, Anthropic, or OpenAI key.'];
        }

        if (!str_starts_with($medium->mime_type, 'image/')) {
            return ['error' => 'Media is not an image.'];
        }

        $filePath = $medium->optimizedPath();
        $base64 = null;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            try {
                $fileData = \Illuminate\Support\Facades\Storage::disk('public')->get($filePath);
                $base64 = base64_encode($fileData);
            } catch (\Exception $ex) {
                \Illuminate\Support\Facades\Log::warning('Failed to load image for Base64 encoding: ' . $ex->getMessage());
            }
        }

        if (empty($base64)) {
            return ['error' => 'Could not read image file from storage.'];
        }

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
                    $prompt = "Analyze this image and return a JSON object with: 'alt_text' (a concise, descriptive alt text for SEO and accessibility) and 'file_name' (a slugified, descriptive SEO-friendly filename without extension, e.g. 'blue-shoes-on-wood'). Return ONLY a valid JSON object. Example: {\"alt_text\": \"Description of image\", \"file_name\": \"slugified-filename-without-extension\"}";
                    $maxRetries = 3;
                    $attempt = 0;
                    while ($attempt < $maxRetries) {
                        $attempt++;
                        $response = Http::timeout(60)->post(
                            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}",
                            [
                                'contents' => [
                                    [
                                        'parts' => [
                                            ['text' => $prompt],
                                            [
                                                'inlineData' => [
                                                    'mimeType' => $medium->mime_type,
                                                    'data' => $base64,
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'generationConfig' => [
                                    'responseMimeType' => 'application/json',
                                    'temperature' => 0.4,
                                ]
                            ]
                        );

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
                        $err = $response->json('error.message') ?? 'Gemini API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Media Auto-Fill: Gemini failed: {$err}");
                        $errorMsg = "Gemini: {$err}";
                    }
                } elseif ($provider === 'anthropic') {
                    $prompt = "Analyze this image and return a JSON object with: 'alt_text' (a concise, descriptive alt text for SEO and accessibility) and 'file_name' (a slugified, descriptive SEO-friendly filename without extension, e.g. 'blue-shoes-on-wood'). Return ONLY a valid JSON object. Do not include markdown code fences or other text. Example: {\"alt_text\": \"Description of image\", \"file_name\": \"slugified-filename-without-extension\"}";
                    $response = Http::timeout(60)
                        ->withHeaders([
                            'x-api-key' => $anthropicKey,
                            'anthropic-version' => '2023-06-01',
                            'content-type' => 'application/json',
                        ])
                        ->post('https://api.anthropic.com/v1/messages', [
                            'model' => 'claude-3-5-sonnet-20241022',
                            'max_tokens' => 300,
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'content' => [
                                        [
                                            'type' => 'image',
                                            'source' => [
                                                'type' => 'base64',
                                                'media_type' => $medium->mime_type,
                                                'data' => $base64,
                                            ]
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $prompt
                                        ]
                                    ]
                                ]
                            ],
                            'temperature' => 0.4,
                        ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['content'][0]['text'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'Anthropic API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Media Auto-Fill: Anthropic failed: {$err}");
                        $errorMsg = "Anthropic: {$err}";
                    }
                } elseif ($provider === 'openai') {
                    $isLocal = app()->environment('local') || str_contains(request()->getHost(), 'localhost') || str_contains(request()->getHost(), '127.0.0.1');
                    if ($isLocal) {
                        $imageUrl = 'data:' . $medium->mime_type . ';base64,' . $base64;
                    } else {
                        $imageUrl = asset('storage/' . $filePath);
                    }

                    $response = Http::timeout(60)->withToken($openAiKey)->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'response_format' => ['type' => 'json_object'],
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => "Analyze this image and return a JSON object with: 'alt_text' (a concise, descriptive alt text for SEO and accessibility) and 'file_name' (a slugified, descriptive SEO-friendly filename without extension, e.g. 'blue-shoes-on-wood'). Return ONLY raw JSON.",
                                    ],
                                    [
                                        'type' => 'image_url',
                                        'image_url' => [
                                            'url' => $imageUrl,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'max_tokens' => 200,
                    ]);

                    if ($response instanceof \Illuminate\Http\Client\Promises\LazyPromise) {
                        $response = $response->wait();
                    }

                    if ($response->successful()) {
                        $body = $response->json();
                        $text = $body['choices'][0]['message']['content'] ?? null;
                    } else {
                        $err = $response->json('error.message') ?? 'OpenAI API status: ' . $response->status();
                        \Illuminate\Support\Facades\Log::warning("Media Auto-Fill: OpenAI failed: {$err}");
                        $errorMsg = "OpenAI: {$err}";
                    }
                }

                if (!empty($text)) {
                    // Try to parse the response text to JSON
                    if (preg_match('/\{.*\}/s', $text, $matches)) {
                        $jsonString = $matches[0];
                    } else {
                        $jsonString = preg_replace('/```json|```/', '', $text);
                    }

                    $data = json_decode(trim($jsonString), true);
                    if (is_array($data)) {
                        return $this->saveMediaMetadata($medium, $data);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Media Auto-Fill: Failed to parse AI response for {$provider}: " . substr($text, 0, 100));
                        $errorMsg = "{$provider}: Response failed to parse as JSON.";
                    }
                }

            } catch (\Exception $ex) {
                \Illuminate\Support\Facades\Log::warning("Media Auto-Fill: {$provider} call threw exception: " . $ex->getMessage());
                $errorMsg = "{$provider}: Exception: " . $ex->getMessage();
            }
        }

        return ['error' => 'All AI providers failed. Last error: ' . $errorMsg];
    }

    public function saveMediaMetadata(Media $medium, array $data): array
    {
        $altText = trim($data['alt_text'] ?? '');
        $slugFilename = \Illuminate\Support\Str::slug($data['file_name'] ?? '');

        if ($slugFilename) {
            $extension = pathinfo($medium->file_name, PATHINFO_EXTENSION);
            $newFilename = $slugFilename . '.' . $extension;
        } else {
            $newFilename = $medium->file_name;
        }

        $medium->update([
            'alt_text' => $altText ?: $medium->alt_text,
            'file_name' => $newFilename
        ]);

        return [
            'success' => true,
            'alt_text' => $medium->alt_text,
            'file_name' => $medium->file_name
        ];
    }
}
