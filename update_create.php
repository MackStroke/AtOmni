<?php
$file = 'resources/views/admin/homepage-sections/create.blade.php';
$content = file_get_contents($file);

// Replace tag_id select
$tagHtmlOld = <<<EOD
                      <select name="tag_id" id="tag_id" class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                          <option value="">All Tags</option>
                          @foreach(\$tags as \$tag)
                              <option value="{{ \$tag->id }}" {{ old('tag_id') == \$tag->id ? 'selected' : '' }}>{{ \$tag->name }}</option>
                          @endforeach
                      </select>
                      @error('tag_id')<p class="mt-1 text-xs text-alert-red">{{ \$message }}</p>@enderror
EOD;

$tagHtmlNew = <<<EOD
                      @php
                          \$selectedTags = old('filters.tag_ids', []);
                      @endphp
                      <select name="filters[tag_ids][]" id="tag_ids" multiple class="w-full px-4 py-2.5 bg-navy-900 border border-navy-700 rounded-xl text-text-primary focus:ring-2 focus:ring-electric focus:border-transparent transition-all">
                          <option value="">Select Tags</option>
                          @foreach(\$tags as \$tag)
                              <option value="{{ \$tag->id }}" {{ in_array(\$tag->id, \$selectedTags) ? 'selected' : '' }}>{{ \$tag->name }}</option>
                          @endforeach
                      </select>
                      @error('filters.tag_ids')<p class="mt-1 text-xs text-alert-red">{{ \$message }}</p>@enderror
EOD;

$content = str_replace($tagHtmlOld, $tagHtmlNew, $content);

// Append TomSelect scripts
$tomSelectHtml = <<<EOD
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* TomSelect Dark Theme Adjustments */
    .ts-control {
        background-color: #1a233a !important; /* navy-900 */
        border-color: #2a3449 !important; /* navy-700 */
        color: #f1f5f9 !important;
        border-radius: 0.75rem !important; /* rounded-xl */
        padding: 0.625rem 1rem !important;
    }
    .ts-dropdown, .ts-control, .ts-dropdown.plugin-optgroup_columns .ts-dropdown-content {
        color: #f1f5f9 !important;
    }
    .ts-dropdown {
        background-color: #1a233a !important;
        border-color: #2a3449 !important;
        border-radius: 0.75rem !important;
        overflow: hidden;
    }
    .ts-dropdown .active {
        background-color: #2a3449 !important;
        color: #38bdf8 !important; /* electric */
    }
    .ts-control input {
        color: #f1f5f9 !important;
    }
    .ts-wrapper.multi .ts-control > div {
        background: #0ea5e9;
        color: white;
        border: none;
        border-radius: 4px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#category_id', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
        new TomSelect('#tag_ids', {
            create: false,
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    });
</script>
EOD;

$content = str_replace('@endsection', $tomSelectHtml . "\n@endsection", $content);

file_put_contents($file, $content);
echo "Create view updated\n";
