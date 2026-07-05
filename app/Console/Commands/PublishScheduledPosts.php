<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature   = 'posts:publish-scheduled';
    protected $description = 'Publish all scheduled posts whose publish date has arrived.';

    public function handle(): int
    {
        $posts = Post::where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->whereNull('deleted_at')
            ->where('kill_switch', false)
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish.');
            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            $post->update(['status' => 'published']);
            $this->line("  ✓ Published post #{$post->id}: {$post->title}");
        }

        $this->info("Published {$posts->count()} post(s).");
        return self::SUCCESS;
    }
}
