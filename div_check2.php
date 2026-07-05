<?php
$c = file_get_contents('resources/views/admin/settings/global.blade.php');

$start1 = strpos($c, '<form id="section-identity"');
$end1 = strpos($c, '</form>', $start1);
$f1 = substr($c, $start1, $end1 - $start1);
echo "Identity Form open: " . substr_count($f1, '<div') . " close: " . substr_count($f1, '</div') . "\n";

$start2 = strpos($c, '<form id="section-theme"');
$end2 = strpos($c, '</form>', $start2);
$f2 = substr($c, $start2, $end2 - $start2);
echo "Theme Form open: " . substr_count($f2, '<div') . " close: " . substr_count($f2, '</div') . "\n";
