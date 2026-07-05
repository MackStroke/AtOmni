<?php
$c = file_get_contents('resources/views/admin/settings/global.blade.php');

$start1 = strpos($c, '<form id="section-identity"');
$end1 = strpos($c, '</form>', $start1);
$f1 = substr($c, $start1, $end1 - $start1);

// Parse the form line by line and find where the div count becomes negative
$lines = explode("\n", $f1);
$open = 0;
$close = 0;
foreach ($lines as $i => $line) {
    $open += substr_count($line, '<div');
    $close += substr_count($line, '</div');
    echo "Line " . ($i + 1) . ": open=$open, close=$close\n";
}
