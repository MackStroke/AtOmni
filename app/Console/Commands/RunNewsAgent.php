<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Setting;
use App\Services\NewsAgentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RunNewsAgent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'agent:run-news
                            {--category= : Run only for a specific category slug}
                            {--dry-run : Research and compose without saving to database}
                            {--force : Run even if the automated agent is disabled in settings}';

    /**
     * The console command description.
     */
    protected $description = 'Run the autonomous AI News Agent to research, write, and publish breaking news';

    /**
     * Execute the console command.
     */
    public function handle(NewsAgentService $agentService): int
    {
        $categorySlug = $this->option('category');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Check if agent is enabled in settings (unless forced or dry-run)
        $isEnabled = Setting::get('news_agent_enabled', 'true') === 'true';
        if (!$isEnabled && !$force && !$dryRun) {
            $this->warn('AI News Agent is currently disabled in Settings.');
            return self::SUCCESS;
        }

        // Initialize logging to custom file for streaming to Admin UI
        $logPath = storage_path('logs/news-agent-run.log');
        File::ensureDirectoryExists(dirname($logPath));
        File::put($logPath, "=== AI News Agent Run Started at " . now()->toDateTimeString() . " ===\n");
        if ($dryRun) {
            File::append($logPath, "[TEST MODE] Running in Dry Run (no database saves).\n");
        }

        $this->info('Starting AI News Agent...');
        
        // Resolve categories to run
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                $msg = "❌ Error: Category with slug '{$categorySlug}' not found.";
                $this->error($msg);
                File::append($logPath, $msg . "\n");
                return self::FAILURE;
            }
            $categories = collect([$category]);
        } else {
            // Get categories configured in settings, or fallback to default major categories
            $configuredSlugs = Setting::get('news_agent_categories', '');
            if (!empty($configuredSlugs)) {
                $slugs = array_filter(array_map('trim', explode(',', $configuredSlugs)));
                $categories = Category::whereIn('slug', $slugs)->get();
            } else {
                // Default fallback categories
                $defaultSlugs = ['world', 'politics', 'technology', 'business', 'science', 'sports', 'health', 'entertainment'];
                $categories = Category::whereIn('slug', $defaultSlugs)->get();
            }
        }

        if ($categories->isEmpty()) {
            $msg = "❌ Warning: No categories resolved for news agent run.";
            $this->warn($msg);
            File::append($logPath, $msg . "\n");
            return self::SUCCESS;
        }

        $totalCategories = $categories->count();
        $this->info("Processing {$totalCategories} categories...");
        File::append($logPath, "Resolved {$totalCategories} categories to process.\n\n");

        $publishedCount = 0;

        foreach ($categories as $index => $cat) {
            $num = $index + 1;
            $this->line("========================================");
            $this->info("Category {$num}/{$totalCategories}: {$cat->name}");
            
            $categoryLog = "";
            try {
                $post = $agentService->runForCategory($cat, $categoryLog, $dryRun);
                if ($post && !$dryRun) {
                    $publishedCount++;
                }
            } catch (\Throwable $e) {
                $categoryLog .= "❌ Exception occurred while processing: " . $e->getMessage() . "\n";
                $categoryLog .= $e->getTraceAsString() . "\n";
                Log::error("NewsAgent category run failed: " . $e->getMessage(), ['exception' => $e]);
            }

            // Append to command output and log file
            $this->line($categoryLog);
            File::append($logPath, $categoryLog);

            // Avoid hitting LLM API rate limits on loop
            if ($num < $totalCategories) {
                $this->line("Waiting 5 seconds before next category...");
                File::append($logPath, "Waiting 5 seconds before next category...\n\n");
                sleep(5);
            }
        }

        $summary = "=== AI News Agent Run Finished at " . now()->toDateTimeString() . " ===\n" .
                   "Summary: Published {$publishedCount} articles.\n";
        
        $this->info($summary);
        File::append($logPath, "\n" . $summary);
        
        if (!$dryRun) {
            Setting::set('news_agent_last_run_at', now()->toDateTimeString());
        }

        return self::SUCCESS;
    }
}
