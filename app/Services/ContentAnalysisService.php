<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ContentAnalysisService
{
    /**
     * Check content sentences for plagiarism against the article archive.
     */
    public function checkPlagiarism(array $sentences, ?int $postId = null): array
    {
        // Get all published posts (excluding current one)
        $query = Post::select('id', 'title', 'content', 'slug');
        if ($postId) {
            $query->where('id', '!=', $postId);
        }
        $posts = $query->get();

        $checks = [];
        $matchedCount = 0;
        $totalChecked = count($sentences);

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) < 20) continue;

            $bestMatch = null;
            $bestPercent = 0;

            foreach ($posts as $post) {
                $postText = strip_tags($post->content);
                // Use similar_text for fuzzy matching on chunks
                $chunks = str_split($postText, strlen($sentence) + 50);
                foreach ($chunks as $chunk) {
                    similar_text(strtolower($sentence), strtolower($chunk), $percent);
                    if ($percent > $bestPercent) {
                        $bestPercent = $percent;
                        $bestMatch = $post;
                    }
                }
            }

            if ($bestPercent >= 80) {
                $matchedCount++;
                $checks[] = [
                    'label' => Str::limit($sentence, 60),
                    'status' => 'bad',
                    'detail' => round($bestPercent) . '% match with "' . Str::limit($bestMatch->title, 40) . '"',
                ];
            } elseif ($bestPercent >= 50) {
                $checks[] = [
                    'label' => Str::limit($sentence, 60),
                    'status' => 'ok',
                    'detail' => round($bestPercent) . '% partial match found in archive.',
                ];
            }
        }

        if (empty($checks)) {
            $checks[] = [
                'label' => "$totalChecked sentences checked",
                'status' => 'good',
                'detail' => 'No significant matches found in your article archive.',
            ];
        }

        $score = $totalChecked > 0 ? max(0, 100 - ($matchedCount / $totalChecked * 100)) : 100;
        $score = round($score);
        $grade = $score >= 90 ? 'Original' : ($score >= 70 ? 'Likely Original' : ($score >= 50 ? 'Needs Review' : 'Flagged'));
        $color = $score >= 90 ? 'green' : ($score >= 70 ? 'blue' : ($score >= 50 ? 'yellow' : 'red'));

        return compact('score', 'grade', 'color', 'checks');
    }

    /**
     * Analyze SEO and AEO using Gemini.
     */
    public function analyzeSeo(string $title, string $content): array
    {
        $post = new Post([
            'title' => $title,
            'content' => $content,
        ]);
        
        // These properties require the GeneratePostScores command logic if we want real scores.
        // However, in the controller they were just reading the default attributes on a new Post instance, 
        // which defaults to null, or maybe it was triggering a mutator?
        // Wait, the original code had:
        // $seoScore = $post->seo_score; // this is likely null on a new instance unless mutators exist.
        // Let's preserve original logic.
        $seoScore = $post->seo_score ?? 50; 
        $aeoScore = $post->aeo_score ?? 50;
        
        $suggestions = [];
        
        if ($seoScore < 70 || $aeoScore < 70) {
            $geminiKey = Setting::where('key', 'gemini_key')->value('value');
            if ($geminiKey) {
                $prompt = "You are an SEO and AEO expert. Analyze this article.\nTitle: {$title}\nContent:\n{$content}\n\nReturn ONLY a JSON object with: 'headline_suggestion' (a better SEO headline), 'seo_improvements' (array of 3 short actionable points), 'aeo_improvements' (array of 3 short points for Answer Engine Optimization). No markdown, no other text.";
                
                try {
                    $response = Http::timeout(60)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}",
                        [
                            'contents' => [['parts' => [['text' => $prompt]]]],
                            'generationConfig' => [
                                'responseMimeType' => 'application/json',
                                'temperature' => 0.7,
                            ],
                        ]
                    );
                    $body = $response->json();
                    $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        $suggestions = json_decode($text, true) ?: [];
                    }
                } catch (\Exception $e) {
                    Log::warning('SEO analysis failed: ' . $e->getMessage());
                }
            }
        }

        return [
            'seo_score' => $seoScore,
            'aeo_score' => $aeoScore,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Generate FAQs for an article using Gemini.
     */
    public function suggestFaqs(string $title, string $content): array
    {
        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        if (!$geminiKey) {
            throw new \Exception('API key not configured.');
        }

        $prompt = "Analyze this article and return 4 FAQs as a JSON array. Each item needs 'question' and 'answer'. Prioritize questions that start with 'What is', 'How to', 'Can you' — these get cited 3x more in Perplexity results. Answers must be 1-2 sentences, factual, no marketing.\n\nTitle: {$title}\n\nBody:\n" . Str::limit(strip_tags($content), 4000);

        // Using Cache::remember as requested to save credits (hashing the content for cache key)
        $cacheKey = 'faq-suggest-' . md5($title . Str::limit($content, 1000));
        
        return Cache::remember($cacheKey, 86400, function() use ($geminiKey, $prompt) {
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}",
                [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.7,
                    ],
                ]
            );
            $body = $response->json();
            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            return json_decode($text, true) ?? [];
        });
    }
}
