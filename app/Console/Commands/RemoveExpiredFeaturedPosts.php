<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;

class RemoveExpiredFeaturedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:remove-expired-featured';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically remove the featured flag from posts whose featured_until date has passed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Post::where('is_featured', true)
                     ->whereNotNull('featured_until')
                     ->where('featured_until', '<', now())
                     ->update([
                         'is_featured' => false,
                         'featured_until' => null,
                     ]);

        $this->info("Successfully removed featured flag from {$count} expired posts.");
    }
}
