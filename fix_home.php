<?php

$filePath = 'resources/views/home.blade.php';
$content = file_get_contents($filePath);

// 1. Remove the incorrectly placed dynamic section at the top
$wrongBlock = <<<HTML
{{-- ╔══════════════════════════════════════════════════════════════════════
     ║  DYNAMIC HOMEPAGE SECTIONS                                           ║
     ╚══════════════════════════════════════════════════════════════════════ --}}
@if(isset(\$dynamicSections) && \$dynamicSections->count() > 0)
    @foreach(\$dynamicSections as \$section)
        @include('partials.homepage-section', ['section' => \$section])
    @endforeach
@endif
HTML;

$content = str_replace($wrongBlock, '', $content);

// 2. Insert it before SPECIFIC CATEGORY SECTIONS
$target = <<<HTML
{{-- ╔══════════════════════════════════════════════════════════════════════
     ║  SPECIFIC CATEGORY SECTIONS                                 ║
HTML;

$correctBlock = $wrongBlock . "\n\n" . $target;

$content = str_replace($target, $correctBlock, $content);

file_put_contents($filePath, $content);
echo "Fixed home.blade.php\n";
