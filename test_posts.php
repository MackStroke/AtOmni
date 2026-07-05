<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = App\Models\Post::where('category_id', 3)->whereHas('tags', function($q) { $q->where('tags.name', 'Automated Research'); })->count();
echo "Matching Posts: " . $posts . "\n";
