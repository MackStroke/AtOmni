<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────────────
        // 1. HEADER NAVBAR — Top-level nav links
        // ──────────────────────────────────────────────────────
        $headerNav = Menu::firstOrCreate(
            ['location' => 'header_navbar'],
            ['name' => 'Header Navigation', 'is_active' => true]
        );

        $navItems = [
            ['title' => 'India',       'url' => '/category/india',       'order' => 1],
            ['title' => 'World',       'url' => '/category/world',       'order' => 2],
            ['title' => 'Business',    'url' => '/category/business',    'order' => 3],
            ['title' => 'Technology',  'url' => '/category/technology',  'order' => 4],
            ['title' => 'Sports',      'url' => '/category/sports',      'order' => 5],
            ['title' => 'Entertainment','url' => '/category/entertainment','order' => 6],
        ];

        foreach ($navItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $headerNav->id, 'title' => $item['title']],
                array_merge($item, ['menu_id' => $headerNav->id])
            );
        }

        // ──────────────────────────────────────────────────────
        // 2. MEGA MENU — Columns with children
        // ──────────────────────────────────────────────────────
        $megaMenu = Menu::firstOrCreate(
            ['location' => 'mega_menu'],
            ['name' => 'Mega Menu', 'is_active' => true]
        );

        $megaCols = [
            'India' => [
                'url' => '/category/india',
                'children' => ['Delhi', 'Mumbai', 'Bangalore', 'Hyderabad'],
            ],
            'World' => [
                'url' => '/category/world',
                'children' => ['USA', 'Europe', 'Middle East', 'Asia Pacific', 'Africa'],
            ],
            'Business' => [
                'url' => '/category/business',
                'children' => ['Markets', 'Startups', 'Economy', 'Real Estate'],
            ],
            'Technology' => [
                'url' => '/category/technology',
                'children' => ['AI & ML', 'Gadgets', 'Apps', 'Cybersecurity'],
            ],
            'Sports' => [
                'url' => '/category/sports',
                'children' => ['Cricket', 'Football', 'Tennis', 'F1', 'Olympics'],
            ],
            'Entertainment' => [
                'url' => '/category/entertainment',
                'children' => ['Bollywood', 'Hollywood', 'OTT', 'Music', 'Television'],
            ],
            'Science' => [
                'url' => '/category/science',
                'children' => ['Space', 'Health', 'Environment', 'Research'],
            ],
            'Lifestyle' => [
                'url' => '/category/lifestyle',
                'children' => ['Travel', 'Food', 'Fashion', 'Wellness'],
            ],
        ];

        $order = 0;
        foreach ($megaCols as $colTitle => $colData) {
            $order++;
            $parent = MenuItem::firstOrCreate(
                ['menu_id' => $megaMenu->id, 'title' => $colTitle, 'parent_id' => null],
                ['menu_id' => $megaMenu->id, 'title' => $colTitle, 'url' => $colData['url'], 'order' => $order]
            );

            $childOrder = 0;
            foreach ($colData['children'] as $child) {
                $childOrder++;
                $slug = \Illuminate\Support\Str::slug($child);
                MenuItem::firstOrCreate(
                    ['menu_id' => $megaMenu->id, 'parent_id' => $parent->id, 'title' => $child],
                    ['menu_id' => $megaMenu->id, 'parent_id' => $parent->id, 'title' => $child, 'url' => '/search?q=' . urlencode($child), 'order' => $childOrder]
                );
            }
        }

        // ──────────────────────────────────────────────────────
        // 3. FOOTER — Company Column
        // ──────────────────────────────────────────────────────
        $footerCompany = Menu::firstOrCreate(
            ['location' => 'footer_company'],
            ['name' => 'Footer — Company', 'is_active' => true]
        );
        $companyItems = [
            ['title' => 'About Us',    'url' => '/about',     'order' => 1],
            ['title' => 'Contact Us',  'url' => '/contact',   'order' => 2],
            ['title' => 'Careers',     'url' => '/careers',   'order' => 3],
            ['title' => 'Advertise',   'url' => '/advertise', 'order' => 4],
            ['title' => 'Press Kit',   'url' => '/press-kit', 'order' => 5],
        ];
        foreach ($companyItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerCompany->id, 'title' => $item['title']],
                array_merge($item, ['menu_id' => $footerCompany->id])
            );
        }

        // ──────────────────────────────────────────────────────
        // 4. FOOTER — Categories Column
        // ──────────────────────────────────────────────────────
        $footerCats = Menu::firstOrCreate(
            ['location' => 'footer_categories'],
            ['name' => 'Footer — Categories', 'is_active' => true]
        );
        $catItems = [
            ['title' => 'India',          'url' => '/category/india',          'order' => 1],
            ['title' => 'World',          'url' => '/category/world',          'order' => 2],
            ['title' => 'Business',       'url' => '/category/business',       'order' => 3],
            ['title' => 'Technology',     'url' => '/category/technology',     'order' => 4],
            ['title' => 'Sports',         'url' => '/category/sports',         'order' => 5],
            ['title' => 'Entertainment',  'url' => '/category/entertainment',  'order' => 6],
            ['title' => 'Science',        'url' => '/category/science',        'order' => 7],
            ['title' => 'Lifestyle',      'url' => '/category/lifestyle',      'order' => 8],
        ];
        foreach ($catItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerCats->id, 'title' => $item['title']],
                array_merge($item, ['menu_id' => $footerCats->id])
            );
        }

        // ──────────────────────────────────────────────────────
        // 5. FOOTER — Legal Column
        // ──────────────────────────────────────────────────────
        $footerLegal = Menu::firstOrCreate(
            ['location' => 'footer_legal'],
            ['name' => 'Footer — Legal', 'is_active' => true]
        );
        $legalItems = [
            ['title' => 'Privacy Policy',  'url' => '/privacy',       'order' => 1],
            ['title' => 'Terms of Service','url' => '/terms',         'order' => 2],
            ['title' => 'Cookie Policy',   'url' => '/cookies',       'order' => 3],
            ['title' => 'DMCA',            'url' => '/dmca',          'order' => 4],
            ['title' => 'Accessibility',   'url' => '/accessibility', 'order' => 5],
        ];
        foreach ($legalItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerLegal->id, 'title' => $item['title']],
                array_merge($item, ['menu_id' => $footerLegal->id])
            );
        }
    }
}
