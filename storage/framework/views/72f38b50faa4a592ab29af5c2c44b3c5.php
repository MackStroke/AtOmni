<?php $__env->startSection('title', 'Atomni — ' . $category->name . ' News'); ?>
<?php $__env->startSection('meta-description', 'Latest ' . $category->name . ' news, analysis, and breaking stories from Atomni.'); ?>
<?php $__env->startSection('canonical', url('category/' . $category->slug)); ?>

<?php $siteName = \App\Models\Setting::get('site_name', 'Atomni'); ?>
<?php $__env->startSection('schema'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "<?php echo e(e($category->name)); ?> News — <?php echo e(e($siteName)); ?>",
    "description": "<?php echo e(e($category?->description ?? 'Latest ' . strtolower($category->name) . ' news, expert analysis, and in-depth reporting.')); ?>",
    "url": "<?php echo e(url('category/' . $category->slug)); ?>",
    "inLanguage": "en",
    "isPartOf": {
        "@type": "WebSite",
        "name": "<?php echo e(e($siteName)); ?>",
        "url": "<?php echo e(url('/')); ?>"
    }
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "<?php echo e(url('/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "<?php echo e(e($category->name)); ?>",
            "item": "<?php echo e(url('category/' . $category->slug)); ?>"
        }
    ]
}
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<section class="relative overflow-hidden py-16 mb-8">
    <div class="absolute inset-0 bg-gradient-to-br from-electric-dark via-navy-900 to-navy-950 opacity-90"></div>
    <div class="absolute inset-0 theme-radial-glow-15"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
            <a href="<?php echo e(url('/')); ?>" class="hover:text-electric transition-colors">Home</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-primary"><?php echo e($category->name); ?></span>
        </nav>
        <h1 class="font-heading font-bold text-4xl sm:text-5xl text-white mb-3"><?php echo e($category->name); ?></h1>
        <p class="text-text-secondary text-lg max-w-2xl">
            <?php echo e($category?->description ?? 'Stay informed with the latest ' . strtolower($category->name) . ' news, expert analysis, and in-depth reporting.'); ?>

        </p>
    </div>
</section>

<?php if($slug === 'sports' && isset($fixtures) && count($fixtures) > 0): ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1 h-7 bg-electric rounded-full"></div>
            <h2 class="font-heading font-bold text-2xl text-text-primary flex items-center gap-3">
                <a href="https://www.thehindu.com/sport/football/fifa-world-cup/" class="hover:text-electric transition-colors" target="_blank">FIFA WC 2026</a>
                <img class="h-6 w-auto object-contain" src="https://sportstar.thehindu.com/static/content/images/20260610093334/fifa-logo.gif" alt="FIFA WC 2026 Logo">
            </h2>
        </div>
        
        <div class="hidden sm:flex items-center gap-2">
            <button class="w-10 h-10 rounded-full bg-navy-800 hover:bg-electric text-text-primary flex items-center justify-center transition-colors border border-navy-700" onclick="document.getElementById('fifa-fixtures').scrollBy({left: -300, behavior: 'smooth'})">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button class="w-10 h-10 rounded-full bg-navy-800 hover:bg-electric text-text-primary flex items-center justify-center transition-colors border border-navy-700" onclick="document.getElementById('fifa-fixtures').scrollBy({left: 300, behavior: 'smooth'})">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <div class="relative w-full">
        <div id="fifa-fixtures" class="flex overflow-x-auto gap-4 pb-6 snap-x snap-mandatory scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                #fifa-fixtures::-webkit-scrollbar { display: none; }
            </style>

            <?php $__currentLoopData = $fixtures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a <?php if($fixture->link): ?> href="<?php echo e($fixture->link); ?>" target="_blank" <?php endif; ?> class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg cursor-pointer">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase <?php echo e($fixture->match_status == 'Upcoming' ? 'text-text-muted' : 'text-electric'); ?>">
                    <span><?php echo e($fixture->match_status); ?></span>
                    <span class="text-text-muted"><?php echo e($fixture->match_time->format('D, d M, Y, H:i')); ?></span>
                </div>
                
                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo e($fixture->team_a_logo ?: asset('images/atomni-placeholder.svg')); ?>" alt="<?php echo e($fixture->team_a_abbrev); ?>" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm"><?php echo e($fixture->team_a_abbrev); ?></span>
                        </div>
                        <span class="text-lg font-bold text-text-primary"><?php echo e($fixture->team_a_score ?: '-'); ?></span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo e($fixture->team_b_logo ?: asset('images/atomni-placeholder.svg')); ?>" alt="<?php echo e($fixture->team_b_abbrev); ?>" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm"><?php echo e($fixture->team_b_abbrev); ?></span>
                        </div>
                        <span class="text-lg font-bold text-text-primary"><?php echo e($fixture->team_b_score ?: '-'); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <?php if(is_countable($posts) && count($posts) > 0 || !is_countable($posts) && $posts): ?>
                    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(url('article/' . $post->slug)); ?>" class="block">
                        <article class="glass-card rounded-xl overflow-hidden group cursor-pointer h-full">
                            <div class="relative h-44 overflow-hidden">
                                <img loading="lazy" src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-bold uppercase bg-electric/90 text-white"><?php echo e($category->name); ?></span>
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-heading font-semibold text-base text-text-primary leading-snug mb-2 group-hover:text-electric-light transition-colors line-clamp-2">
                                    <?php echo e($post->title); ?>

                                </h3>
                                <p class="text-text-secondary text-sm leading-relaxed mb-3 line-clamp-2"><?php echo e($post->excerpt); ?></p>
                                <div class="flex items-center justify-between text-xs text-text-muted">
                                    <span><?php echo e($post->author?->name ?? 'Staff Writer'); ?></span>
                                    <span><?php echo e($post?->reading_time ?? 5); ?> min · <?php echo e($post->published_at?->diffForHumans() ?? ''); ?></span>
                                </div>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <p class="text-text-muted text-lg">No articles in this category yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            
            <?php if($posts->hasPages()): ?>
            <div class="mt-10">
                <?php echo e($posts->links('partials.pagination')); ?>

            </div>
            <?php endif; ?>
        </div>

        
        <aside class="space-y-6">
            <div class="glass-card rounded-xl p-6">
                <h3 class="font-heading font-bold text-lg text-text-primary mb-2">📬 Stay Informed</h3>
                <p class="text-text-secondary text-sm mb-4">Get <?php echo e(strtolower($category->name)); ?> news delivered to your inbox.</p>
                <form action="<?php echo e(route('subscribe')); ?>" method="POST" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <input type="email" name="email" autocomplete="email" placeholder="your@email.com" required class="w-full px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">Subscribe Free</button>
                </form>
            </div>

            
            <?php echo $__env->make('partials.donate-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        </aside>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/pages/category.blade.php ENDPATH**/ ?>