<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$catPosts = App\Models\Post::where('category_id', 3)->count();
$tagPosts = App\Models\Post::whereHas('tags', function($q) { $q->where('tags.name', 'Automated Research'); })->count();
echo "Posts in Category 3: " . $catPosts . "\n";
echo "Posts with Tag 'Automated Research': " . $tagPosts . "\n";
