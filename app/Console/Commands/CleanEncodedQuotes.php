<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanEncodedQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-encoded-quotes';

    protected $description = 'Clean double-encoded HTML entities (like &#039;) from the database tables.';

    public function handle()
    {
        $this->info("Cleaning Posts...");
        foreach (\App\Models\Post::all() as $post) {
            $post->title = $this->cleanString($post->title);
            $post->excerpt = $this->cleanString($post->excerpt);
            $post->content = $this->cleanString($post->content);
            $post->saveQuietly();
        }

        $this->info("Cleaning Categories...");
        foreach (\App\Models\Category::all() as $cat) {
            $cat->name = $this->cleanString($cat->name);
            $cat->description = $this->cleanString($cat->description);
            $cat->saveQuietly();
        }

        $this->info("Cleaning Tags...");
        foreach (\App\Models\Tag::all() as $tag) {
            $tag->name = $this->cleanString($tag->name);
            $tag->saveQuietly();
        }

        $this->info("Cleaning Settings...");
        foreach (\App\Models\Setting::all() as $setting) {
            if (is_string($setting->value)) {
                $setting->value = $this->cleanString($setting->value);
                $setting->saveQuietly();
            }
        }

        $this->info("✅ Database successfully cleaned of double-encoded quotes!");
    }

    private function cleanString($string)
    {
        if (!$string) return $string;
        $string = str_replace(['&amp;#039;', '&#039;', '&#039', '&amp;#39;', '&#39;', '&#39'], "'", $string);
        $string = str_replace(['&amp;quot;', '&quot;', '&#034;', '&#34;'], '"', $string);
        return $string;
    }
}
