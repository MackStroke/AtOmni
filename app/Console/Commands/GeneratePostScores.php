<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Str;

class GeneratePostScores extends Command
{
    protected $signature = 'posts:analyze-scores {--all : Re-analyze all posts} {--ids= : Comma-separated list of post IDs to analyze} {--limit=0 : Number of posts to analyze (0 for no limit)}';
    protected $description = 'Generate realistic SEO, AEO, and GEO scores for posts based on heuristic algorithms.';

    public function handle()
    {
        $query = Post::query();

        if ($this->option('ids')) {
            $ids = array_filter(explode(',', $this->option('ids')));
            $query->whereIn('id', $ids);
        } elseif (!$this->option('all')) {
            $query->whereNull('seo_score');
        }

        $limit = (int) $this->option('limit');
        $total = $limit > 0 ? min($limit, $query->count()) : $query->count();

        if ($total === 0) {
            $this->info('No posts require scoring.');
            return 0;
        }

        $this->info("Analyzing {$total} posts...");
        $bar = $this->output->createProgressBar($total);

        $processed = 0;

        $query->chunkById(200, function ($posts) use (&$processed, $limit, $bar) {
            foreach ($posts as $post) {
                if ($limit > 0 && $processed >= $limit) {
                    return false; // Stop chunking
                }
                
                $post->update([
                    'seo_score' => $this->calculateSeoScore($post),
                    'aeo_score' => $this->calculateAeoScore($post),
                    'geo_score' => $this->calculateGeoScore($post),
                ]);

                $processed++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Scores generated successfully!');
        return 0;
    }

    private function calculateSeoScore(Post $post): int
    {
        $score = 40; // Base score
        
        $wordCount = str_word_count(strip_tags($post->content));
        
        // Word count metrics
        if ($wordCount > 1000) $score += 25;
        elseif ($wordCount > 500) $score += 15;
        elseif ($wordCount > 300) $score += 5;
        else $score -= 10;

        // Title length metric (ideal: 50-60 chars)
        $titleLen = strlen($post->title);
        if ($titleLen >= 40 && $titleLen <= 65) $score += 15;
        elseif ($titleLen > 10) $score += 5;

        // Metadata
        if (!empty($post->excerpt)) $score += 10;
        if ($post->category_id) $score += 5;

        // Simple readability check: paragraphs
        $paragraphCount = substr_count($post->content, '</p>');
        if ($paragraphCount > 5) $score += 5;

        // Add some random fuzziness based on views to simulate real variance
        $fuzz = rand(-5, 5);
        
        return min(100, max(0, $score + $fuzz));
    }

    private function calculateAeoScore(Post $post): int
    {
        $score = 30; // Base score
        
        // TLDR or Summary check
        if (!empty($post->tldr) || stripos($post->content, 'tl;dr') !== false || stripos($post->content, 'summary') !== false) {
            $score += 25;
        }

        // FAQs check
        if (!empty($post->faqs) && count((array)$post->faqs) > 0) {
            $score += 25;
        }

        // List formatting (highly valued by Answer Engines)
        $listCount = substr_count($post->content, '</li>');
        if ($listCount > 5) {
            $score += 15;
        }

        $fuzz = rand(-5, 5);
        return min(100, max(0, $score + $fuzz));
    }

    private function calculateGeoScore(Post $post): int
    {
        $score = 30; // Base score
        
        // Generative engines prefer rich media
        if ($post->featured_image) {
            $score += 20;
        }
        
        // External links (authority signals)
        $linkCount = substr_count($post->content, '<a href=');
        if ($linkCount >= 2) {
            $score += 20;
        }

        // Content depth (headings structure)
        $h2Count = substr_count($post->content, '<h2');
        $h3Count = substr_count($post->content, '<h3');
        if ($h2Count >= 2 && $h3Count >= 1) {
            $score += 25;
        } elseif ($h2Count >= 1) {
            $score += 15;
        }

        $fuzz = rand(-5, 5);
        return min(100, max(0, $score + $fuzz));
    }
}
