<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Location;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaxonomyService
{
    /**
     * Analyze a single post to suggest taxonomy.
     * Returns an array with 'category_id', 'tags', and 'locations'.
     */
    public function suggestForPost(string $title, string $content): array
    {
        $results = $this->suggestForPosts([
            ['id' => 'single', 'title' => $title, 'content' => $content]
        ]);

        return $results['single'] ?? $this->fallbackSuggestion($title, $content);
    }

    /**
     * Analyze multiple posts in one API call.
     * $posts should be an array of ['id' => ..., 'title' => ..., 'content' => ...]
     * Returns an associative array keyed by post ID.
     */
    public function suggestForPosts(array $posts): array
    {
        if (empty($posts)) {
            return [];
        }

        $geminiKey = Setting::where('key', 'gemini_key')->value('value');
        if (!$geminiKey) {
            return $this->fallbackBulk($posts);
        }

        $categories = Category::select('id', 'name')->get();
        $tags = Tag::select('name')->get();
        $locations = Location::select('id', 'name')->get();

        $prompt = "You are an intelligent taxonomy classifier. I will provide a list of posts with ID, Title, and Content excerpts. " .
                  "Assign the best matching Category ID, an array of up to 3 Tag names (can be existing or new), and an array of Location IDs to each post.\n" .
                  "Categories available: \n";
        foreach ($categories as $cat) {
            $prompt .= "{$cat->id} - {$cat->name}\n";
        }
        $prompt .= "\nLocations available: \n";
        foreach ($locations as $loc) {
            $prompt .= "{$loc->id} - {$loc->name}\n";
        }
        $prompt .= "\nReturn ONLY a JSON object where the key is the post ID, and the value is an object like: {\"category_id\": 1, \"tags\": [\"AI\", \"Tech\"], \"locations\": [2, 5]}.\n" .
                   "If you cannot confidently determine a category, return null for category_id. If no tags or locations fit, return empty arrays.\n\nPosts:\n";

        foreach ($posts as $post) {
            $excerpt = substr(strip_tags($post['content']), 0, 800); // Send only up to 800 chars to save tokens
            $prompt .= "ID: {$post['id']} | Title: {$post['title']} | Content: {$excerpt}\n\n";
        }

        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $geminiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.2,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                $text = preg_replace('/```json|```/', '', $text);
                $mapping = json_decode(trim($text), true);

                if (is_array($mapping)) {
                    return $mapping;
                }
            }
        } catch (\Exception $e) {
            Log::error('Taxonomy AI Error: ' . $e->getMessage());
        }

        // Fallback if API fails
        return $this->fallbackBulk($posts);
    }

    private function fallbackBulk(array $posts): array
    {
        $results = [];
        foreach ($posts as $post) {
            $results[$post['id']] = $this->fallbackSuggestion($post['title'], $post['content']);
        }
        return $results;
    }

    private function fallbackSuggestion(string $title, string $content): array
    {
        $text = strtolower($title . ' ' . strip_tags($content));
        
        // Category
        $categoryId = null;
        foreach (Category::all() as $cat) {
            if (str_contains($text, strtolower($cat->name))) {
                $categoryId = $cat->id;
                break;
            }
        }
        if (!$categoryId) {
            $categoryId = Category::first()?->id;
        }

        // Tags
        $suggestedTags = [];
        foreach (Tag::all() as $tag) {
            if (str_contains($text, strtolower($tag->name))) {
                $suggestedTags[] = $tag->name;
            }
        }

        // Locations
        $suggestedLocations = [];
        foreach (Location::all() as $loc) {
            if (str_contains($text, strtolower($loc->name))) {
                $suggestedLocations[] = $loc->id;
            }
        }

        return [
            'category_id' => $categoryId,
            'tags' => $suggestedTags,
            'locations' => $suggestedLocations,
        ];
    }
}
