<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pages = \App\Models\Page::pluck('slug')->toArray();
echo "Pages found: " . implode(', ', $pages);
