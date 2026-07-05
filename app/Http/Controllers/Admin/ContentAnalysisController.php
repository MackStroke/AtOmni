<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class ContentAnalysisController extends Controller
{
    public function analyze(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        // Use provided values or fallback to current post values
        $content = $request->input('content', $post->content ?? '');
        $title = $request->input('title', $post->title ?? '');
        $excerpt = $request->input('excerpt', $post->excerpt ?? '');
        
        // This simulates a real API call or advanced heuristic algorithm
        $analysis = $this->runHeuristicAnalysis($post, $content, $title, $excerpt);
        
        // Update the post with new scores
        $post->update([
            'seo_score' => $analysis['seo_score'],
            'aeo_score' => $analysis['aeo_score'],
            'geo_score' => $analysis['geo_score'],
        ]);

        return response()->json($analysis);
    }

    private function runHeuristicAnalysis($post, $content, $title, $excerpt)
    {
        $suggestions = [
            'headline' => [],
            'content' => [],
            'aeo' => [],
            'geo' => []
        ];

        // --- SEO Score ---
        $seoScore = 40;
        $wordCount = str_word_count(strip_tags($content));
        
        if ($wordCount > 1000) {
            $seoScore += 25;
        } elseif ($wordCount > 500) {
            $seoScore += 15;
            $suggestions['content'][] = 'Good length, but expanding to 1000+ words can boost deep ranking.';
        } elseif ($wordCount > 300) {
            $seoScore += 5;
            $suggestions['content'][] = 'Content is a bit thin. Try to expand your article past 500 words for better SEO.';
        } else {
            $seoScore -= 10;
            $suggestions['content'][] = 'CRITICAL: Content is way too short. Google prefers in-depth content. Add more detail.';
        }

        $titleLen = strlen($title);
        if ($titleLen >= 40 && $titleLen <= 65) {
            $seoScore += 15;
        } else {
            if ($titleLen < 40) {
                $suggestions['headline'][] = 'Your title is too short. A longer, descriptive title (50-60 chars) performs better.';
            } else {
                $suggestions['headline'][] = 'Your title is too long. Search engines will truncate it. Keep it under 65 characters.';
            }
        }

        if (!empty($excerpt)) {
            $seoScore += 10;
        } else {
            $suggestions['content'][] = 'Add a custom excerpt. This is often used as the meta description in search results.';
        }

        $paragraphCount = substr_count($content, '</p>');
        if ($paragraphCount > 5) {
            $seoScore += 5;
        } else {
            $suggestions['content'][] = 'Break your text into more paragraphs to improve readability.';
        }

        // --- AEO Score (Answer Engine Optimization) ---
        $aeoScore = 30;
        if (!empty($post->tldr) || stripos($content, 'tl;dr') !== false || stripos($content, 'summary') !== false) {
            $aeoScore += 25;
        } else {
            $suggestions['aeo'][] = 'Add a "TL;DR" or Summary section at the top. Answer engines (like ChatGPT or Perplexity) prioritize quick summaries.';
        }

        if (!empty($post->faqs) && count((array)$post->faqs) > 0) {
            $aeoScore += 25;
        } else {
            $suggestions['aeo'][] = 'Add FAQ schema or an FAQ section. Answer engines use this heavily to answer user queries.';
        }

        $listCount = substr_count($content, '</li>');
        if ($listCount > 5) {
            $aeoScore += 15;
        } else {
            $suggestions['aeo'][] = 'Use more bullet points and lists. AI engines prefer structured, easily scannable data.';
        }

        // --- GEO Score (Generative Engine Optimization) ---
        $geoScore = 30;
        if ($post->featured_image) {
            $geoScore += 20;
        } else {
            $suggestions['geo'][] = 'Add a featured image. Generative engines favor content with rich media.';
        }
        
        $linkCount = substr_count($content, '<a href=');
        if ($linkCount >= 2) {
            $geoScore += 20;
        } else {
            $suggestions['geo'][] = 'Add external outbound links to authoritative sources to build trust signals for AI models.';
        }

        $h2Count = substr_count($content, '<h2');
        $h3Count = substr_count($content, '<h3');
        if ($h2Count >= 2 && $h3Count >= 1) {
            $geoScore += 25;
        } elseif ($h2Count >= 1) {
            $geoScore += 15;
            $suggestions['geo'][] = 'Add H3 subheadings under your H2s to create a deeper, logical structure for AI parsers.';
        } else {
            $suggestions['geo'][] = 'Missing H2 subheadings. Properly structure your document with headings.';
        }

        // Add some slight random fuzziness to simulate dynamic heuristic changes
        $fuzz = rand(-3, 3);

        return [
            'seo_score' => min(100, max(0, $seoScore + $fuzz)),
            'aeo_score' => min(100, max(0, $aeoScore + $fuzz)),
            'geo_score' => min(100, max(0, $geoScore + $fuzz)),
            'suggestions' => $suggestions
        ];
    }
}
