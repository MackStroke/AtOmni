<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AnalyzePostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     * We give AI analysis a generous timeout.
     */
    public $timeout = 600;

    protected $action;
    protected $ids;

    /**
     * Create a new job instance.
     *
     * @param string $action 'taxonomy' or 'scores'
     * @param array $ids Array of post IDs
     */
    public function __construct(string $action, array $ids)
    {
        $this->action = $action;
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->ids)) {
            return;
        }

        $command = $this->action === 'taxonomy' ? 'posts:analyze-taxonomy' : 'posts:analyze-scores';

        try {
            Artisan::call($command, [
                '--ids' => implode(',', $this->ids)
            ]);
            Log::info("AnalyzePostsJob completed {$command} for " . count($this->ids) . " posts.");
        } catch (\Exception $e) {
            Log::error("AnalyzePostsJob failed for {$command}: " . $e->getMessage());
            throw $e;
        }
    }
}
