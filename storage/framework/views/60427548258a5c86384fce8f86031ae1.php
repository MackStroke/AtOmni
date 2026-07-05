<?php $__env->startSection('title', 'Explore Topics — Atomni'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-10">
        <h1 class="font-heading font-bold text-4xl text-text-primary mb-4">Explore Topics</h1>
        <p class="text-text-secondary text-lg">Discover the latest stories across all our categories and tags.</p>
    </div>

    
    <div class="relative max-w-2xl mx-auto mb-16">
        <input type="text" id="topic-search" autocomplete="off" placeholder="Search categories and tags..." class="w-full px-6 py-4 rounded-2xl bg-navy-900 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-2 focus:ring-electric/30 transition-all text-lg shadow-sm">
        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-text-muted">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-electric rounded-full"></div>
            <h2 class="font-heading font-bold text-2xl text-text-primary">Categories</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="categories-grid">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('category', $category->slug)); ?>" class="topic-item category-item group bg-navy-900 border border-navy-700 rounded-xl p-5 flex flex-col items-center justify-center text-center hover:bg-navy-800 transition-colors shadow-sm" data-name="<?php echo e(strtolower($category->name)); ?>">
                    <h3 class="font-heading font-semibold text-text-primary group-hover:text-electric-light transition-colors mb-1"><?php echo e($category->name); ?></h3>
                    <span class="text-xs font-medium px-2 py-1 bg-navy-900 rounded-md text-text-muted"><?php echo e($category->posts_count); ?> posts</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-text-muted col-span-full">No categories found.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-electric rounded-full"></div>
            <h2 class="font-heading font-bold text-2xl text-text-primary">Popular Tags</h2>
        </div>
        <div class="flex flex-wrap gap-3" id="tags-cloud">
            <?php $__empty_1 = true; $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('search', ['tag' => $tag->name])); ?>" class="topic-item tag-item px-4 py-2 rounded-full text-sm font-medium text-text-secondary bg-navy-800 border border-navy-700 hover:bg-electric hover:text-white hover:border-electric transition-all flex items-center gap-2" data-name="<?php echo e(strtolower($tag->name)); ?>">
                    #<?php echo e($tag->name); ?>

                    <span class="text-xs opacity-60"><?php echo e($tag->posts_count); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-text-muted w-full">No tags found.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div id="no-results" class="hidden text-center py-16">
        <p class="text-text-muted text-xl">No topics match your search.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('topic-search');
    const items = document.querySelectorAll('.topic-item');
    const noResults = document.getElementById('no-results');

    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        let visibleCount = 0;

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(query)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/pages/explore.blade.php ENDPATH**/ ?>