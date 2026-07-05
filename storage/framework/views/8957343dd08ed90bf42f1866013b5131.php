<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1">
        
        <?php if($paginator->onFirstPage()): ?>
            <span class="px-3 py-2 rounded-lg text-sm text-text-muted cursor-not-allowed opacity-40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" aria-label="Previous Page" class="px-3 py-2 rounded-lg text-sm text-text-secondary hover:text-text-primary hover:bg-navy-800/60 [.light_&]:hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_string($element)): ?>
                <span class="px-3 py-2 text-sm text-text-muted"><?php echo e($element); ?></span>
            <?php endif; ?>

            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <span class="px-3.5 py-2 rounded-lg text-sm font-bold bg-electric text-white shadow-md shadow-electric/20"><?php echo e($page); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" aria-label="Page <?php echo e($page); ?>" class="px-3.5 py-2 rounded-lg text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-navy-800/60 [.light_&]:hover:bg-slate-100 transition-colors"><?php echo e($page); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" aria-label="Next Page" class="px-3 py-2 rounded-lg text-sm text-text-secondary hover:text-text-primary hover:bg-navy-800/60 [.light_&]:hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        <?php else: ?>
            <span class="px-3 py-2 rounded-lg text-sm text-text-muted cursor-not-allowed opacity-40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/vendor/pagination/atomni.blade.php ENDPATH**/ ?>