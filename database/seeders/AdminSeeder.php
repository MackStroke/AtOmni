<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContactQuery;
use App\Models\Newsletter;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ───────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@atomni.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]
        );

        // ── Categories ───────────────────────────────────
        $categories = collect([
            // ... your existing categories ...
            // Global & Society
            ['name' => 'World', 'slug' => 'world', 'color_code' => '#374151', 'sort_order' => 7],
            ['name' => 'Health', 'slug' => 'health', 'color_code' => '#F43F5E', 'sort_order' => 8],
            ['name' => 'Environment', 'slug' => 'environment', 'color_code' => '#166534', 'sort_order' => 9],

            // Culture & Lifestyle
            ['name' => 'Culture', 'slug' => 'culture', 'color_code' => '#D946EF', 'sort_order' => 10],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'color_code' => '#F97316', 'sort_order' => 11],
            ['name' => 'Travel', 'slug' => 'travel', 'color_code' => '#0EA5E9', 'sort_order' => 12],
            ['name' => 'Food', 'slug' => 'food', 'color_code' => '#BE123C', 'sort_order' => 13],

            // Emerging & Specialized
            ['name' => 'AI & Future', 'slug' => 'ai-future', 'color_code' => '#6366F1', 'sort_order' => 14],
            ['name' => 'Economy', 'slug' => 'economy', 'color_code' => '#15803D', 'sort_order' => 15],
            ['name' => 'Education', 'slug' => 'education', 'color_code' => '#A855F7', 'sort_order' => 16],
            ['name' => 'Investigative', 'slug' => 'investigative', 'color_code' => '#000000', 'sort_order' => 17],
        ])->map(fn($cat) => Category::updateOrCreate(['slug' => $cat['slug']], $cat));

        // Create sub-categories
        $techCat = $categories->firstWhere('slug', 'technology');
        if ($techCat) {
            Category::updateOrCreate(['slug' => 'ai'], ['name' => 'Artificial Intelligence', 'slug' => 'ai', 'parent_id' => $techCat->id, 'color_code' => '#2D7FF9']);
            Category::updateOrCreate(['slug' => 'gadgets'], ['name' => 'Gadgets', 'slug' => 'gadgets', 'parent_id' => $techCat->id, 'color_code' => '#2D7FF9']);
        }

        // ── Tags ─────────────────────────────────────────
        $tags = collect(['AI', 'Climate', 'Economy', 'Elections', 'Health', 'Space', 'Startups', 'Cybersecurity'])
            ->map(fn($name) => Tag::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'slug' => Str::slug($name)]));

        // ── Sample Posts ─────────────────────────────────
        $posts = [
            ['title' => 'The Future of Digital Democracy: How AI Is Reshaping Political Campaigns Worldwide', 'category' => 'politics', 'status' => 'published', 'featured' => true],
            ['title' => 'Quantum Computing Breakthrough Promises 1000x Speed Increase', 'category' => 'technology', 'status' => 'published', 'featured' => false],
            ['title' => 'Inside the Rise of Esports: A $5 Billion Industry', 'category' => 'sports', 'status' => 'published', 'featured' => false],
            ['title' => 'Global Markets Rally as Trade Negotiations Reach Breakthrough', 'category' => 'business', 'status' => 'published', 'featured' => false],
            ['title' => 'SpaceX Successfully Launches Starship for Mars Mission Testing', 'category' => 'science', 'status' => 'published', 'featured' => false],
            ['title' => 'The Ethics of Artificial Intelligence in Journalism', 'category' => 'opinion', 'status' => 'draft', 'featured' => false],
            ['title' => 'Climate Summit 2026: Key Takeaways from the Global Agreement', 'category' => 'politics', 'status' => 'published', 'featured' => false],
            ['title' => 'Cybersecurity Threats in the Age of Remote Work', 'category' => 'technology', 'status' => 'draft', 'featured' => false],
        ];

        foreach ($posts as $p) {
            $cat = $categories->firstWhere('slug', $p['category']);
            $content = "<p>This is sample content for the article \"{$p['title']}\". In a production environment, this would contain the full article body with rich formatting, images, and embedded media.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>";

            $post = Post::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'author_id' => $admin->id,
                    'category_id' => $cat?->id,
                    'title' => $p['title'],
                    'slug' => Str::slug($p['title']),
                    'content' => $content,
                    'excerpt' => Str::limit(strip_tags($content), 150),
                    'status' => $p['status'],
                    'is_featured' => $p['featured'],
                    'reading_time' => rand(3, 12),
                    'views_count' => rand(100, 50000),
                    'trending_score' => rand(1, 100) / 10,
                    'published_at' => $p['status'] === 'published' ? now()->subDays(rand(0, 30)) : null,
                ]
            );

            // Attach random tags
            $post->tags()->syncWithoutDetaching($tags->random(rand(2, 4))->pluck('id'));
        }

        // ── Newsletter Subscribers ───────────────────────
        $emails = ['reader1@example.com', 'news.fan@example.com', 'tech.lover@example.com', 'john.doe@example.com', 'jane.smith@example.com', 'daily.digest@example.com'];
        foreach ($emails as $email) {
            Newsletter::updateOrCreate(['email' => $email], ['subscribed_at' => now()->subDays(rand(1, 60))]);
        }

        // ── Contact Queries ──────────────────────────────
        $queries = [
            ['name' => 'Alice Thompson', 'email' => 'alice@example.com', 'subject' => 'Partnership Inquiry', 'message' => 'Hello, I represent a media company and would love to discuss a potential partnership. Could we schedule a call?', 'status' => 'new'],
            ['name' => 'Bob Chen', 'email' => 'bob@example.com', 'subject' => 'Bug Report: Mobile Layout', 'message' => 'The article page layout breaks on iPhone 12 in landscape mode. The sidebar overlaps the main content.', 'status' => 'new'],
            ['name' => 'Clara Garcia', 'email' => 'clara@example.com', 'subject' => 'Content License Request', 'message' => 'I would like to republish your article on digital democracy. What are the licensing terms?', 'status' => 'read'],
            ['name' => 'Derek Wilson', 'email' => 'derek@example.com', 'subject' => 'Advertising on Atomni', 'message' => 'We are interested in running a banner ad campaign. Could you send the media kit?', 'status' => 'replied'],
        ];
        foreach ($queries as $q) {
            ContactQuery::updateOrCreate(['email' => $q['email'], 'subject' => $q['subject']], $q);
        }

        // ── RSS Settings ─────────────────────────────────
        Setting::set('rss_enabled', 'true');
        Setting::set('rss_title', 'Atomni News Feed');
        Setting::set('rss_description', 'Latest news and articles from Atomni');
        Setting::set('rss_max_items', '25');
        Setting::set('rss_custom_urls', '');

        // ── Pages ────────────────────────────────────────
        $pages = [
            ['title' => 'About Us', 'slug' => 'about-us', 'content' => '<h1>About Atomni</h1><p>Atomni is your premium source for news and analysis.</p>', 'is_published' => true],
            ['title' => 'Contact', 'slug' => 'contact', 'content' => '<h1>Contact Us</h1><p>Get in touch with our editorial team.</p>', 'is_published' => true],
            ['title' => 'Advertise', 'slug' => 'advertise', 'content' => '<h1>Advertising on Atomni</h1><p>Reach millions of readers weekly.</p>', 'is_published' => false]
        ];
        foreach ($pages as $pg) {
            \App\Models\Page::updateOrCreate(['slug' => $pg['slug']], $pg);
        }

        // ── Traffic Reports (Last 14 days dummy data) ────
        for ($i = 14; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            \App\Models\TrafficReport::updateOrCreate(
                ['report_date' => $date],
                [
                    'page_views' => rand(5000, 15000),
                    'unique_visitors' => rand(2000, 8000),
                    'data_consumed_mb' => rand(100, 1000) / 10 + rand(100, 500)
                ]
            );
        }
    }
}
