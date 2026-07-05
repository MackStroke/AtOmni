<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Tag;
use App\Services\TaxonomyService;
use Illuminate\Support\Str;

class GeneratePostTaxonomy extends Command
{
    protected $signature = 'posts:analyze-taxonomy {--all : Re-analyze all posts (otherwise only uncategorized)} {--ids= : Comma-separated list of post IDs to analyze} {--limit=0 : Number of posts to analyze (0 for no limit)}';
    protected $description = 'Auto-fill taxonomy (categories, tags, locations) for posts.';

    public function handle(TaxonomyService $taxonomyService)
    {
        $query = Post::query();

        if ($this->option('ids')) {
            $ids = array_filter(explode(',', $this->option('ids')));
            $query->whereIn('id', $ids);
        } elseif (!$this->option('all')) {
            $query->whereNull('category_id');
        }

        $limit = (int) $this->option('limit');
        $total = $limit > 0 ? min($limit, $query->count()) : $query->count();

        if ($total === 0) {
            $this->info('No posts require taxonomy analysis.');
            return 0;
        }

        $this->info("Analyzing taxonomy for {$total} posts...");
        $bar = $this->output->createProgressBar($total);

        $processed = 0;

        // Process in smaller chunks to avoid API payload limits (e.g., 20 posts per chunk)
        $query->chunkById(20, function ($posts) use (&$processed, $limit, $bar, $taxonomyService) {
            
            $postsData = $posts->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                ];
            })->toArray();
            
            $suggestions = $taxonomyService->suggestForPosts($postsData);
            
            foreach ($posts as $item) {
                if ($limit > 0 && $processed >= $limit) {
                    return false; // Stop chunking
                }
                
                $sug = $suggestions[$item->id] ?? null;
                if ($sug) {
                    // Category
                    if (empty($item->category_id) && !empty($sug['category_id'])) {
                        $item->category_id = $sug['category_id'];
                        $item->save();
                    }
                    
                    // Tags
                    if ($item->tags()->count() === 0 && !empty($sug['tags'])) {
                        $tagIds = collect($sug['tags'])->map(function ($tagName) {
                            return Tag::firstOrCreate(
                                ['name' => $tagName], 
                                ['slug' => Str::slug($tagName)]
                            )->id;
                        });
                        $item->tags()->sync($tagIds);
                    }
                    
                    // Locations
                    if ($item->locations()->count() === 0 && !empty($sug['locations'])) {
                        $item->locations()->sync($sug['locations']);
                    }
                }

                $processed++;
                $bar->advance();
            }
            
            // Sleep slightly to prevent API rate limits if using AI
            sleep(2);
        });

        $bar->finish();
        $this->newLine();
        $this->info('Taxonomy generated successfully!');
        return 0;
    }
}
