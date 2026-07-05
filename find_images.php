<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$iter = new RecursiveIteratorIterator($dir);
foreach ($iter as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $c = file_get_contents($file->getPathname());
        if (strpos($c, 'featured_image') !== false) {
            echo $file->getPathname() . "\n";
        }
    }
}
