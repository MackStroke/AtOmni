<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Total Posts</p>
        <p class="stat-number text-text-primary"><?php echo e(number_format($stats['total_posts'])); ?></p>
        <p class="text-xs text-text-muted mt-1 truncate"><?php echo e($stats['published_posts']); ?> published · <?php echo e($stats['draft_posts']); ?> drafts</p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Total Views</p>
        <p class="stat-number text-text-primary"><?php echo e(number_format($stats['total_views'])); ?></p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">Subscribers</p>
        <p class="stat-number text-electric"><?php echo e(number_format($stats['subscribers'])); ?></p>
    </div>
    <div class="glass-card rounded-xl p-4 sm:p-5">
        <p class="text-text-muted text-xs font-medium uppercase tracking-wider mb-1">New Contacts</p>
        <p class="stat-number <?php echo e($stats['new_contacts'] > 0 ? 'text-alert-red' : 'text-text-primary'); ?>"><?php echo e($stats['new_contacts']); ?></p>
        <?php if($stats['pending_comments'] > 0): ?>
            <p class="text-xs text-amber mt-1"><?php echo e($stats['pending_comments']); ?> comments pending</p>
        <?php endif; ?>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-navy-700/30">
            <h2 class="font-heading font-semibold text-text-primary">Recent Posts</h2>
            <a href="<?php echo e(route('admin.posts.index')); ?>" class="text-xs text-electric hover:text-electric-light transition-colors focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-800 rounded-sm">View All →</a>
        </div>
        <div class="divide-y divide-navy-700/20">
            <?php if(is_countable($recentPosts) && count($recentPosts) > 0 || !is_countable($recentPosts) && $recentPosts): ?>
                <?php $__currentLoopData = $recentPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-text-primary truncate"><?php echo e($post->title); ?></p>
                        <p class="text-xs text-text-muted"><?php echo e($post->category?->name ?? 'Uncategorized'); ?> · <?php echo e($post->created_at->diffForHumans()); ?></p>
                    </div>
                    <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide
                        <?php echo e($post->status === 'published' ? 'bg-success/15 text-success' : ($post->status === 'scheduled' ? 'bg-amber/15 text-amber' : 'bg-navy-600/30 text-text-muted')); ?>">
                        <?php echo e($post->status); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p class="px-5 py-6 text-sm text-text-muted text-center">No posts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-navy-700/30">
            <h2 class="font-heading font-semibold text-text-primary">Contact Queries</h2>
            <a href="<?php echo e(route('admin.contacts.index')); ?>" class="text-xs text-electric hover:text-electric-light transition-colors focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-800 rounded-sm">View All →</a>
        </div>
        <div class="divide-y divide-navy-700/20">
            <?php if(is_countable($recentContacts) && count($recentContacts) > 0 || !is_countable($recentContacts) && $recentContacts): ?>
                <?php $__currentLoopData = $recentContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.contacts.show', data_get($contact, 'id', 1))); ?>" class="block px-5 py-3 hover:bg-navy-800/30 transition-colors focus:outline-none focus:bg-navy-800/50 focus:ring-2 focus:ring-inset focus:ring-electric">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-text-primary truncate"><?php echo e(data_get($contact, 'subject', 'No Subject')); ?></p>
                            <p class="text-xs text-text-muted"><?php echo e(data_get($contact, 'name')); ?> · <?php echo e(\Carbon\Carbon::parse(data_get($contact, 'created_at'))->diffForHumans()); ?></p>
                        </div>
                        <?php if(data_get($contact, 'status') === 'new'): ?>
                            <span class="shrink-0 w-2 h-2 rounded-full bg-electric animate-pulse"></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p class="px-5 py-6 text-sm text-text-muted text-center">No queries yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="glass-card rounded-xl overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-navy-700/30">
        <h2 class="font-heading font-semibold text-text-primary">Recent Comments</h2>
    </div>
    <div class="divide-y divide-navy-700/20">
        <?php if(is_countable($recentComments) && count($recentComments) > 0 || !is_countable($recentComments) && $recentComments): ?>
            <?php $__currentLoopData = $recentComments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-medium text-text-primary"><?php echo e($comment->displayName()); ?></span>
                    <span class="text-xs text-text-muted">on "<?php echo e(\Illuminate\Support\Str::limit($comment->post?->title ?? 'Deleted', 40)); ?>"</span>
                    <?php if (! ($comment->is_approved)): ?>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber/15 text-amber uppercase">Pending</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-text-secondary line-clamp-1"><?php echo e($comment->comment_text); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="px-5 py-6 text-sm text-text-muted text-center">No comments yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>