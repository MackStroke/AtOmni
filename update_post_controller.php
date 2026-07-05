<?php
$file = 'app/Http/Controllers/Admin/PostController.php';
$content = file_get_contents($file);

// Replace Category::ordered()->get() with Category::orderBy('name')->get()
$content = str_replace("Category::ordered()->get()", "Category::orderBy('name')->get()", $content);

file_put_contents($file, $content);
echo "PostController updated\n";
