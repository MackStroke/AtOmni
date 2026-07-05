<?php
$c = file_get_contents('resources/views/admin/settings/global.blade.php');
echo "open: " . substr_count($c, '<div') . "\n";
echo "close: " . substr_count($c, '</div') . "\n";
