<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$iter = new RecursiveIteratorIterator($dir);
foreach ($iter as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $c = file_get_contents($file->getPathname());
        $original = $c;
        
        $c = str_replace('Storage::url($post->featured_image)', '$post->featuredImageUrl()', $c);
        $c = str_replace('\Illuminate\Support\Facades\Storage::url($post->featured_image)', '$post->featuredImageUrl()', $c);
        $c = str_replace("asset('storage/' . \$post->featured_image)", '$post->featuredImageUrl()', $c);
        $c = str_replace('asset("storage/" . $post->featured_image)', '$post->featuredImageUrl()', $c);

        if ($c !== $original) {
            file_put_contents($file->getPathname(), $c);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done replacing featured_image URLs in views.\n";
