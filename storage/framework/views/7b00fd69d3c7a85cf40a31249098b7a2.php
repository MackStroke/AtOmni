<?php if($paginator->hasPages()): ?>
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-2">
    
    <?php if($paginator->onFirstPage()): ?>
        <span class="px-3 py-2 rounded-lg bg-navy-800/50 text-text-muted text-sm cursor-default">← Prev</span>
    <?php else: ?>
        <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="px-3 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors">← Prev</a>
    <?php endif; ?>

    
    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_string($element)): ?>
            <span class="px-2 text-text-muted text-sm"><?php echo e($element); ?></span>
        <?php endif; ?>

        <?php if(is_array($element)): ?>
            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $paginator->currentPage()): ?>
                    <span class="px-4 py-2 rounded-lg bg-electric text-white text-sm font-semibold"><?php echo e($page); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($url); ?>" class="px-4 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors"><?php echo e($page); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if($paginator->hasMorePages()): ?>
        <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="px-3 py-2 rounded-lg bg-navy-800 text-text-secondary hover:bg-navy-700 hover:text-text-primary text-sm font-medium transition-colors flex items-center gap-1">
            Next <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    <?php else: ?>
        <span class="px-3 py-2 rounded-lg bg-navy-800/50 text-text-muted text-sm cursor-default">Next →</span>
    <?php endif; ?>
</nav>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/partials/pagination.blade.php ENDPATH**/ ?>