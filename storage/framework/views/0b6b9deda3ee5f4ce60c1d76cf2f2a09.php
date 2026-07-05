<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'column' => '', // The database column to sort by
    'label' => '',  // The display text
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'column' => '', // The database column to sort by
    'label' => '',  // The display text
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentSort = request('sort');
    $currentDir = request('dir', 'desc');
    
    // Determine the next direction if we click this header
    $nextDir = 'asc';
    if ($currentSort === $column) {
        $nextDir = $currentDir === 'asc' ? 'desc' : 'asc';
    }

    $isSorted = $currentSort === $column;
?>

<a href="<?php echo e(request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir])); ?>" class="group inline-flex items-center gap-1.5 hover:text-white light:hover:text-slate-900 transition-colors">
    <?php echo e($label); ?>

    
    <span class="flex flex-col items-center justify-center -space-y-1">
        <svg class="w-3 h-3 <?php echo e($isSorted && $currentDir === 'asc' ? 'text-electric' : 'text-text-muted/50 group-hover:text-text-muted light:group-hover:text-slate-500'); ?>" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 8h16z"/></svg>
        <svg class="w-3 h-3 <?php echo e($isSorted && $currentDir === 'desc' ? 'text-electric' : 'text-text-muted/50 group-hover:text-text-muted light:group-hover:text-slate-500'); ?>" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-8H4z"/></svg>
    </span>
</a>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/components/admin/sort-header.blade.php ENDPATH**/ ?>