<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $content = file_get_contents($file);
        
        $pattern = '/<div class="w-full h-full bg-navy-800 flex items-center justify-center">\s*<svg[^>]*>.*?<\/svg>\s*<\/div>/is';
        $replacement = '<img src="{{ asset(\'images/atomni-placeholder.svg\') }}" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">';
        
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== null && $newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "Updated: " . $file . "\n";
        }
    }
}
