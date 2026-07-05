<?php
$file = 'resources/views/components/admin/bulk-actions.blade.php';
$content = file_get_contents($file);

$uniqueFunction = <<<EOD
window.bulkSelectContext = window.bulkSelectContext || {};

function getUniqueCount(scope, selector) {
    let boxes = scope.querySelectorAll(selector);
    let unique = new Set(Array.from(boxes).map(cb => cb.value));
    return unique.size;
}

document.addEventListener('DOMContentLoaded', function() {
EOD;

$content = str_replace("window.bulkSelectContext = window.bulkSelectContext || {};\n\ndocument.addEventListener('DOMContentLoaded', function() {", $uniqueFunction, $content);

$content = str_replace(
    "const all = scope.querySelectorAll('.bulk-item-checkbox').length;",
    "const all = getUniqueCount(scope, '.bulk-item-checkbox');",
    $content
);
$content = str_replace(
    "const checked = scope.querySelectorAll('.bulk-item-checkbox:checked').length;",
    "const checked = getUniqueCount(scope, '.bulk-item-checkbox:checked');",
    $content
);
$content = str_replace(
    "const checkedCount = scope.querySelectorAll('.bulk-item-checkbox:checked').length;",
    "const checkedCount = getUniqueCount(scope, '.bulk-item-checkbox:checked');",
    $content
);
$content = str_replace(
    "const allItemsCount = scope.querySelectorAll('.bulk-item-checkbox').length;",
    "const allItemsCount = getUniqueCount(scope, '.bulk-item-checkbox');",
    $content
);
$content = str_replace(
    "let checkedCount = scope.querySelectorAll('.bulk-item-checkbox:checked').length;",
    "let checkedCount = getUniqueCount(scope, '.bulk-item-checkbox:checked');",
    $content
);

// Update finalIds logic
$oldFinalIds = <<<EOD
            } else {
                const checkedBoxes = scope.querySelectorAll('.bulk-item-checkbox:checked');
                checkedBoxes.forEach(cb => finalIds.push(cb.value));
            }
EOD;
$newFinalIds = <<<EOD
            } else {
                const checkedBoxes = scope.querySelectorAll('.bulk-item-checkbox:checked');
                const uniqueSet = new Set(Array.from(checkedBoxes).map(cb => cb.value));
                finalIds = Array.from(uniqueSet);
            }
EOD;
$content = str_replace($oldFinalIds, $newFinalIds, $content);

file_put_contents($file, $content);
echo "bulk-actions updated\n";

$indexFile = 'resources/views/admin/posts/index.blade.php';
$indexContent = file_get_contents($indexFile);

$oldFinalIdsIndex = <<<EOD
        } else {
            const checkedBoxes = document.querySelectorAll('.bulk-item-checkbox:checked');
            checkedBoxes.forEach(cb => finalIds.push(cb.value));
        }
EOD;
$newFinalIdsIndex = <<<EOD
        } else {
            const checkedBoxes = document.querySelectorAll('.bulk-item-checkbox:checked');
            const uniqueSet = new Set(Array.from(checkedBoxes).map(cb => cb.value));
            finalIds = Array.from(uniqueSet);
        }
EOD;
$indexContent = str_replace($oldFinalIdsIndex, $newFinalIdsIndex, $indexContent);

file_put_contents($indexFile, $indexContent);
echo "index updated\n";
