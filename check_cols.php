<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$postsCols = \Illuminate\Support\Facades\Schema::getColumnListing('posts');
echo "Posts Columns: " . implode(', ', $postsCols) . "\n";
