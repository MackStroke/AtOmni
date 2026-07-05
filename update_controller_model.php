<?php
$file = 'app/Http/Controllers/Admin/HomepageSectionController.php';
$content = file_get_contents($file);

// Add validation rules for filters.tag_ids in store and update methods
$storeReplace = <<<EOD
            'filters' => 'nullable|array',
            'filters.tag_ids' => 'nullable|array',
            'filters.tag_ids.*' => 'exists:tags,id',
EOD;
$content = str_replace("'filters' => 'nullable|array',", $storeReplace, $content);

file_put_contents($file, $content);
echo "Controller updated\n";

$modelFile = 'app/Models/HomepageSection.php';
$modelContent = file_get_contents($modelFile);

$modelReplace = <<<EOD
        if (\$this->tag_id) {
            \$query->whereHas('tags', function (\$q) {
                \$q->where('tags.id', \$this->tag_id);
            });
        }

        if (!empty(\$this->filters['tag_ids'])) {
            \$query->whereHas('tags', function (\$q) {
                \$q->whereIn('tags.id', \$this->filters['tag_ids']);
            });
        }
EOD;

$modelPattern = <<<EOD
        if (\$this->tag_id) {
            \$query->whereHas('tags', function (\$q) {
                \$q->where('tags.id', \$this->tag_id);
            });
        }
EOD;

$modelContent = str_replace($modelPattern, $modelReplace, $modelContent);
file_put_contents($modelFile, $modelContent);
echo "Model updated\n";
