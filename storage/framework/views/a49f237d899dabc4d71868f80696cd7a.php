<?php
    $siteName = \App\Models\Setting::get('site_name', 'Atomni');
    $tagline = \App\Models\Setting::get('website_tagline', 'Breaking News, Analysis & Trending Stories');
?>
<?php $__env->startSection('title', $siteName . ' — ' . $tagline); ?>


<?php if(isset($dynamicSections) && $dynamicSections->count() > 0): ?>

<section id="dynamic-sections" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-12">
    <?php $__currentLoopData = $dynamicSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $sectionPosts = $section->getPosts(); ?>
        <?php if($sectionPosts->count() > 0): ?>
        <div class="border-b border-navy-700/50 pb-12 last:border-0 last:pb-0">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-heading font-bold text-xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                    <?php if($section->category): ?>
                        <a href="<?php echo e(route('category', $section->category->slug)); ?>" class="hover:text-electric transition-colors"><?php echo e($section->title); ?></a>
                        <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <?php else: ?>
                        <span><?php echo e($section->title); ?></span>
                    <?php endif; ?>
                </h2>
            </div>
            
            <?php if($section->layout_type == 'grid'): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php else: ?>
                <div class="flex overflow-x-auto gap-6 pb-4 snap-x">
            <?php endif; ?>
            
                <?php $__currentLoopData = $sectionPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="block group flex flex-col <?php echo e($section->layout_type != 'grid' ? 'shrink-0 w-[280px] snap-start' : ''); ?>">
                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-3">
                        <?php if($post->featured_image): ?>
                            <img src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <?php else: ?>
                            <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 to-transparent opacity-50 group-hover:opacity-80 transition-opacity"></div>
                    </div>
                    <h3 class="font-heading font-semibold text-sm text-text-primary group-hover:text-electric transition-colors line-clamp-3">
                        <?php echo e($post->title); ?>

                    </h3>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>

<?php $lcpImage = collect($featuredPosts)->first()?->featuredImageUrl() ?? ''; ?>
<?php if($lcpImage): ?>
<?php $__env->startSection('lcp-preload'); ?>
    <link rel="preload" as="image" href="<?php echo e($lcpImage); ?>" fetchpriority="high">
<?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('schema'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "@id": "<?php echo e(url('/')); ?>",
    "name": "<?php echo e(e($siteName)); ?> — <?php echo e(e($tagline)); ?>",
    "description": "<?php echo e(e($tagline)); ?>",
    "url": "<?php echo e(url('/')); ?>",
    "inLanguage": "en-IN",
    "isPartOf": {
        "@type": "WebSite",
        "name": "<?php echo e(e($siteName)); ?>",
        "url": "<?php echo e(url('/')); ?>"
    },
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "<?php echo e(url('/')); ?>"
        }]
    }
}
</script>
<?php if($featuredPosts->count() > 0): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Featured News",
    "url": "<?php echo e(url('/')); ?>",
    "itemListElement": [
        <?php $__currentLoopData = $featuredPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $fp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            "@type": "ListItem",
            "position": <?php echo e($i + 1); ?>,
            "url": "<?php echo e(route('frontend.article', $fp->slug)); ?>",
            "name": "<?php echo e(e($fp->title)); ?>"
        }<?php echo e(!$loop->last ? ',' : ''); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ]
}
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<h1 class="sr-only"><?php echo e($siteName); ?> - <?php echo e($tagline); ?></h1>




<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        
        <div class="lg:col-span-3">
            <?php if(collect($featuredPosts)->isNotEmpty()): ?>
            <div class="relative w-full h-full min-h-[320px] lg:min-h-[420px] rounded-2xl overflow-hidden group/carousel" id="featured-carousel">
                <?php $__currentLoopData = $featuredPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $featuredPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('frontend.article', $featuredPost->slug)); ?>" 
                   class="carousel-slide absolute inset-0 transition-opacity duration-1000 block focus:outline-none <?php echo e($index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'); ?>">
                    <article class="relative group rounded-2xl overflow-hidden cursor-pointer h-full w-full">
                        <div class="absolute inset-0 bg-navy-800">
                            
                            
                            <img
                                src="<?php echo e($featuredPost->featuredImageUrl()); ?>"
                                alt="<?php echo e($featuredPost->title); ?>"
                                class="w-full h-full object-cover opacity-60 group-hover:opacity-70 group-hover:scale-105 transition-all duration-700"
                                <?php if($index === 0): ?>
                                    fetchpriority="high"
                                    loading="eager"
                                    decoding="sync"
                                <?php else: ?>
                                    loading="lazy"
                                    decoding="async"
                                <?php endif; ?>
                            >
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/60 to-transparent"></div>
                        <div class="relative flex flex-col justify-end h-full p-6 lg:p-8" style="z-index:10">
                            <div class="flex items-center gap-2 mb-3">
                                <?php if($featuredPost->category): ?>
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-electric text-white"><?php echo e($featuredPost->category->name); ?></span>
                                <?php endif; ?>
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-amber text-navy-950">Featured</span>
                            </div>
                            <h2 class="font-heading font-bold text-2xl sm:text-3xl lg:text-4xl text-text-primary leading-tight mb-3 group-hover:text-electric-light transition-colors">
                                <?php echo e($featuredPost->title); ?>

                            </h2>
                            <p class="text-text-secondary text-sm sm:text-base leading-relaxed mb-4 line-clamp-2 max-w-2xl">
                                <?php echo e($featuredPost->excerpt); ?>

                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-navy-700 overflow-hidden">
                                    <?php if($featuredPost->author && $featuredPost->author->avatar): ?>
                                        <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($featuredPost->author->avatar)); ?>" alt="<?php echo e($featuredPost->author->name); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-xs font-bold text-white bg-electric"><?php echo e(substr($featuredPost->author?->name ?? 'A', 0, 1)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-sm">
                                    <span class="text-text-primary font-medium"><?php echo e($featuredPost->author?->name ?? 'Staff Writer'); ?></span>
                                    <span class="text-text-muted mx-1.5">·</span>
                                    <span class="text-text-muted"><?php echo e($featuredPost?->reading_time ?? 5); ?> min read</span>
                                    <span class="text-text-muted mx-1.5">·</span>
                                    <span class="text-text-muted"><?php echo e($featuredPost->published_at?->diffForHumans() ?? 'Recently'); ?></span>
                                </div>
                            </div>
                        </div>
                    </article>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                
                <?php if(collect($featuredPosts)->count() > 1): ?>
                <div class="absolute bottom-4 right-6 flex items-center gap-1 z-20">
                    <?php $__currentLoopData = $featuredPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button class="carousel-indicator group relative flex items-center justify-center w-6 h-6 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Slide <?php echo e($index + 1); ?>">
                        <span class="block rounded-full transition-all duration-300 <?php echo e($index === 0 ? 'bg-electric w-5 h-2' : 'bg-white/40 group-hover:bg-white/70 w-2 h-2'); ?>"></span>
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                
                <button id="carousel-prev" aria-label="Previous slide" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-navy-950/50 backdrop-blur-sm text-white flex items-center justify-center hover:bg-electric transition-colors z-20 opacity-0 group-hover/carousel:opacity-100">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="carousel-next" aria-label="Next slide" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-navy-950/50 backdrop-blur-sm text-white flex items-center justify-center hover:bg-electric transition-colors z-20 opacity-0 group-hover/carousel:opacity-100">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const carousel = document.getElementById('featured-carousel');
                        if (!carousel) return;
                        const slides = carousel.querySelectorAll('.carousel-slide');
                        const indicators = carousel.querySelectorAll('.carousel-indicator');
                        let currentSlide = 0;
                        let slideInterval;
                        
                        function showSlide(index) {
                            slides.forEach(s => { s.classList.remove('opacity-100', 'z-10'); s.classList.add('opacity-0', 'z-0'); });
                            // Reset all indicator inner spans
                            if (indicators.length) indicators.forEach(i => {
                                var dot = i.querySelector('span');
                                if (dot) { dot.classList.remove('bg-electric', 'w-5', 'h-2'); dot.classList.add('bg-white/40', 'w-2', 'h-2'); }
                            });
                            
                            slides[index].classList.remove('opacity-0', 'z-0');
                            slides[index].classList.add('opacity-100', 'z-10');
                            
                            if (indicators.length) {
                                var activeDot = indicators[index].querySelector('span');
                                if (activeDot) { activeDot.classList.remove('bg-white/40', 'w-2'); activeDot.classList.add('bg-electric', 'w-5', 'h-2'); }
                            }
                            
                            currentSlide = index;
                        }
                        
                        function nextSlide() { showSlide((currentSlide + 1) % slides.length); }
                        function prevSlide() { showSlide((currentSlide - 1 + slides.length) % slides.length); }
                        function startSlideShow() { slideInterval = setInterval(nextSlide, 5000); }
                        
                        const btnNext = document.getElementById('carousel-next');
                        const btnPrev = document.getElementById('carousel-prev');
                        
                        if (btnNext) btnNext.addEventListener('click', (e) => { e.preventDefault(); clearInterval(slideInterval); nextSlide(); startSlideShow(); });
                        if (btnPrev) btnPrev.addEventListener('click', (e) => { e.preventDefault(); clearInterval(slideInterval); prevSlide(); startSlideShow(); });
                        
                        indicators.forEach((ind, i) => {
                            ind.addEventListener('click', (e) => { e.preventDefault(); clearInterval(slideInterval); showSlide(i); startSlideShow(); });
                        });
                        
                        startSlideShow();
                    });
                </script>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="glass-card rounded-2xl p-12 text-center min-h-[320px] lg:min-h-[420px] flex items-center justify-center">
                <p class="text-text-muted text-lg">No featured articles yet. Create your first post!</p>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="lg:col-span-2 flex flex-col gap-4">
            
            <h2 class="sr-only">Top Stories</h2>
            <?php $__empty_1 = true; $__currentLoopData = $secondaryPosts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php /** @var \App\Models\Post $post */ ?>
                <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="flex gap-3 glass-card rounded-xl p-3 group cursor-pointer hover:bg-navy-800/60 transition-colors">
                    <div class="shrink-0 w-24 h-20 rounded-lg overflow-hidden bg-navy-800">
                        <img loading="lazy" src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="flex flex-col justify-center min-w-0">
                        <?php if($post->category): ?>
                        <span class="text-xs font-bold uppercase tracking-wider text-electric mb-1"><?php echo e($post->category?->name); ?></span>
                        <?php endif; ?>
                        <h3 class="text-sm font-semibold text-text-primary leading-snug line-clamp-2 group-hover:text-electric-light transition-colors"><?php echo e($post->title); ?></h3>
                        <span class="text-xs text-text-muted mt-1"><?php echo e($post?->reading_time ?? 5); ?> min read</span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="glass-card rounded-xl p-6 text-center">
                    <p class="text-text-muted text-sm">More articles coming soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>



<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-2 overflow-x-auto scroll-hide pb-3 pt-1 px-1 -mx-1 flex-1 w-full">
            <a href="<?php echo e(route('home')); ?>" class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold bg-electric text-white shadow-lg shadow-electric/20">All</a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('category', $cat->slug)); ?>" class="shrink-0 px-4 py-2 rounded-full text-sm font-medium text-text-secondary bg-navy-800 hover:bg-navy-700 hover:text-text-primary transition-colors">
                    <?php echo e($cat->name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="relative shrink-0 w-full sm:w-64 z-20">
            <label for="location-filter" class="sr-only">Filter news by location</label>
            <select id="location-filter" onchange="if(this.value) window.location.href='/location/'+this.value;" class="w-full appearance-none px-4 py-2 rounded-full text-sm font-medium text-text-primary bg-navy-800 hover:bg-navy-700 transition-colors border border-navy-700 focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric cursor-pointer">
                <option value="">🌍 Global News (All Locations)</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($country->name); ?>">
                        <option value="<?php echo e($country->slug); ?>"><?php echo e($country->name); ?> (Country)</option>
                        <?php $__currentLoopData = $country->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state->slug); ?>"><?php echo e($state->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-text-muted">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
    </div>
</section>




<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-7 bg-electric rounded-full"></div>
        <h2 class="font-heading font-bold text-2xl text-text-primary">Latest Articles</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $latestPosts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php /** @var \App\Models\Post $post */ ?>
            <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="block">
                <article class="glass-card rounded-xl overflow-hidden group cursor-pointer h-full">
                    <div class="relative h-44 overflow-hidden">
                        <img loading="lazy" src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <?php if($post->category): ?>
                        <div class="absolute top-3 left-3">
                            <span class="px-2 py-1 rounded-md text-xs font-bold uppercase bg-electric/90 text-white"><?php echo e($post->category?->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5">
                        <h3 class="font-heading font-semibold text-base text-text-primary leading-snug mb-2 group-hover:text-electric-light transition-colors line-clamp-2">
                            <?php echo e($post->title); ?>

                        </h3>
                        <p class="text-text-secondary text-sm leading-relaxed mb-3 line-clamp-2"><?php echo e($post->excerpt); ?></p>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-navy-700 overflow-hidden">
                                <?php if($post->author && $post->author->avatar): ?>
                                    <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($post->author->avatar)); ?>" alt="<?php echo e($post->author?->name ?? 'Author'); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-xs font-bold text-white bg-electric"><?php echo e(substr($post->author?->name ?? 'A', 0, 1)); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center text-xs text-text-muted">
                                <span><?php echo e($post->author?->name ?? 'Staff Writer'); ?></span>
                                <span class="mx-1.5">·</span>
                                <span><?php echo e($post?->reading_time ?? 5); ?> min read</span>
                            </div>
                        </div>
                    </div>
                </article>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-12">
                <p class="text-text-muted text-lg">No articles published yet.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($latestPosts instanceof \Illuminate\Pagination\AbstractPaginator && $latestPosts->hasPages()): ?>
    <div class="mt-10">
        <?php echo e($latestPosts->links('partials.pagination')); ?>

    </div>
    <?php endif; ?>
</section>


<?php if(!empty($authorSectionEnabled) && $authorSectionEnabled && $authorSectionPosts->count() > 0): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-7 bg-electric rounded-full"></div>
        <h2 class="font-heading font-bold text-2xl text-text-primary"><?php echo e($authorSectionTitle ?? 'Selected Author'); ?></h2>
    </div>
    
    <div class="bg-navy-900/50 rounded-3xl p-6 border border-navy-800">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <?php $__currentLoopData = $authorSectionPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="glass-card rounded-2xl overflow-hidden group flex flex-col">
                <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="block relative aspect-video overflow-hidden">
                    <?php if($post->featured_image): ?>
                        <img src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <?php else: ?>
                        <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>
                
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-heading font-bold text-lg text-text-primary line-clamp-2 group-hover:text-electric transition-colors">
                        <a href="<?php echo e(route('frontend.article', $post->slug)); ?>"><?php echo e($post->title); ?></a>
                    </h3>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($authorSectionPosts->count() > 3): ?>
        <div class="bg-navy-800 rounded-2xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-navy-700">
                <?php $__currentLoopData = $authorSectionPosts->skip(3)->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="px-4 py-2 hover:text-electric transition-colors group flex items-start gap-3">
                    <div class="mt-1 w-2 h-2 rounded-full bg-electric/50 group-hover:bg-electric transition-colors shrink-0"></div>
                    <span class="font-heading font-medium text-sm text-text-primary group-hover:text-electric leading-snug line-clamp-3"><?php echo e($post->title); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>




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
            <button class="w-10 h-10 rounded-full bg-navy-800 hover:bg-electric text-text-primary flex items-center justify-center transition-colors border border-navy-700"
                    onclick="document.getElementById('fifa-fixtures').scrollBy({left: -300, behavior: 'smooth'})">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button class="w-10 h-10 rounded-full bg-navy-800 hover:bg-electric text-text-primary flex items-center justify-center transition-colors border border-navy-700"
                    onclick="document.getElementById('fifa-fixtures').scrollBy({left: 300, behavior: 'smooth'})">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>

    
    <div class="relative w-full">
        <div id="fifa-fixtures" class="flex overflow-x-auto gap-4 pb-6 snap-x snap-mandatory scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
            
            <style>
                #fifa-fixtures::-webkit-scrollbar { display: none; }
            </style>

                        <a href="https://www.thehindu.com/sport/football/fifa-world-cup/mexico-vs-south-africa-live-score-fifa-world-cup-2026-mex-vs-rsa-live-match-updates/article71090888.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">FRI, 12 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t659.png" alt="MEX" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MEX</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t659.png" alt="MEX" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MEX</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/south-korea-vs-czechia-live-score-fifa-world-cup-2026-kor-vs-cze-live-match-updates-june-12-2026/article71092024.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">FRI, 12 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1041.png" alt="KOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">KOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1041.png" alt="KOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">KOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/canada-vs-bosnia-and-herzegovina-live-score-fifa-world-cup-2026-match-updates/article71095109.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SAT, 13 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t597.png" alt="CAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t597.png" alt="CAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/usa-vs-paraguay-live-score-fifa-world-cup-2026-match-updates/article71096240.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SAT, 13 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t596.png" alt="USA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">USA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">4</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t596.png" alt="USA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">USA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">4</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/qatar-vs-switzerland-live-score-fifa-world-cup-2026-goal-updates/article71098781.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SUN, 14 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1873.png" alt="QAT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">QAT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1873.png" alt="QAT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">QAT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/fifa-world-cup-2026-brazil-vs-morocco-live-score-updates/article71098977.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SUN, 14 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t614.png" alt="BRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t614.png" alt="BRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">1</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/fifa-world-cup-2026-haiti-vs-scotland-live-score-updates/article71099957.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SUN, 14 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1327.png" alt="HAI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">HAI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">0</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1327.png" alt="HAI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">HAI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">0</span>
                    </div>
                </div>
            </a>
            <a href="https://www.thehindu.com/sport/football/australia-vs-turkiye-live-score-fifa-world-cup-2026-live-updates/article71100181.ece" class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-electric">
                    <span>Full Time</span>
                    <span class="text-text-muted">SUN, 14 JUN, 2026, 09:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t575.png" alt="AUS" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">AUS</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t575.png" alt="AUS" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">AUS</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">2</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 14 JUN, 2026, 22:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t357.png" alt="DEU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">DEU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t357.png" alt="DEU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">DEU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 15 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t366.png" alt="NED" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NED</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t366.png" alt="NED" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NED</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 15 JUN, 2026, 04:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1221.png" alt="CIV" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CIV</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1221.png" alt="CIV" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CIV</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 15 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t361.png" alt="SWE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SWE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t361.png" alt="SWE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SWE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 15 JUN, 2026, 21:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t118.png" alt="ESP" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ESP</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t118.png" alt="ESP" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ESP</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 16 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t360.png" alt="BEL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BEL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t360.png" alt="BEL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BEL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 16 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1264.png" alt="KSA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">KSA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1264.png" alt="KSA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">KSA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 16 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1042.png" alt="IRN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">IRN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1042.png" alt="IRN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">IRN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 17 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t368.png" alt="FRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">FRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t368.png" alt="FRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">FRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 17 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1800.png" alt="IRQ" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">IRQ</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1800.png" alt="IRQ" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">IRQ</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 17 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t632.png" alt="ARG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ARG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t632.png" alt="ARG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ARG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 17 JUN, 2026, 09:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t515.png" alt="AUT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">AUT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t515.png" alt="AUT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">AUT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 17 JUN, 2026, 22:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t359.png" alt="PRT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PRT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t359.png" alt="PRT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PRT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 18 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t114.png" alt="ENG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ENG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t114.png" alt="ENG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ENG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 18 JUN, 2026, 04:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1219.png" alt="GHA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">GHA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1219.png" alt="GHA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">GHA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 18 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1359.png" alt="UZB" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">UZB</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1359.png" alt="UZB" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">UZB</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 18 JUN, 2026, 21:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t367.png" alt="CZE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CZE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t367.png" alt="CZE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CZE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 19 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t497.png" alt="SUI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SUI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t497.png" alt="SUI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SUI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 19 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t597.png" alt="CAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t597.png" alt="CAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 19 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t659.png" alt="MEX" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MEX</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t659.png" alt="MEX" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MEX</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 20 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t596.png" alt="USA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">USA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t596.png" alt="USA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">USA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 20 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t115.png" alt="SCO" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SCO</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t115.png" alt="SCO" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SCO</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 20 JUN, 2026, 06:00 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t614.png" alt="BRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t614.png" alt="BRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 20 JUN, 2026, 08:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t362.png" alt="TUR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t362.png" alt="TUR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 20 JUN, 2026, 22:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t366.png" alt="NED" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NED</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t366.png" alt="NED" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NED</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 21 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t357.png" alt="DEU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">DEU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t357.png" alt="DEU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">DEU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 21 JUN, 2026, 05:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t830.png" alt="ECU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ECU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t830.png" alt="ECU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ECU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 21 JUN, 2026, 09:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1224.png" alt="TUN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1224.png" alt="TUN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 21 JUN, 2026, 21:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t118.png" alt="ESP" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ESP</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t118.png" alt="ESP" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ESP</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 22 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t360.png" alt="BEL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BEL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t360.png" alt="BEL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BEL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 22 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t837.png" alt="URU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">URU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t837.png" alt="URU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">URU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 22 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1804.png" alt="NZL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NZL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1804.png" alt="NZL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NZL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">MON, 22 JUN, 2026, 22:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t632.png" alt="ARG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ARG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t632.png" alt="ARG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ARG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 23 JUN, 2026, 02:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t368.png" alt="FRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">FRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t368.png" alt="FRA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">FRA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 23 JUN, 2026, 05:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t363.png" alt="NOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t363.png" alt="NOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 23 JUN, 2026, 08:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1843.png" alt="JOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">JOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1843.png" alt="JOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">JOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">TUE, 23 JUN, 2026, 22:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t359.png" alt="PRT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PRT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t359.png" alt="PRT" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PRT</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 24 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t114.png" alt="ENG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ENG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t114.png" alt="ENG" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ENG</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 24 JUN, 2026, 04:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1869.png" alt="PAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1869.png" alt="PAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">WED, 24 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t832.png" alt="COL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">COL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t832.png" alt="COL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">COL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t537.png" alt="BIH" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BIH</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t537.png" alt="BIH" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">BIH</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t497.png" alt="SUI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SUI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t497.png" alt="SUI" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SUI</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t115.png" alt="SCO" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SCO</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t115.png" alt="SCO" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SCO</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 03:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1057.png" alt="MAR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MAR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1057.png" alt="MAR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">MAR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t367.png" alt="CZE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CZE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t367.png" alt="CZE" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CZE</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">THU, 25 JUN, 2026, 06:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t522.png" alt="RSA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">RSA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t522.png" alt="RSA" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">RSA</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t830.png" alt="ECU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ECU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t830.png" alt="ECU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">ECU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 01:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t6512.png" alt="CUW" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CUW</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t6512.png" alt="CUW" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CUW</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 04:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1224.png" alt="TUN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1224.png" alt="TUN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 04:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1266.png" alt="JPN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">JPN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1266.png" alt="JPN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">JPN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t362.png" alt="TUR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t362.png" alt="TUR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">TUR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">FRI, 26 JUN, 2026, 07:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t835.png" alt="PAR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t835.png" alt="PAR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1226.png" alt="SEN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SEN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1226.png" alt="SEN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">SEN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 00:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t363.png" alt="NOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t363.png" alt="NOR" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NOR</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 05:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1529.png" alt="CPV" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CPV</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1529.png" alt="CPV" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">CPV</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 05:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t837.png" alt="URU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">URU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t837.png" alt="URU" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">URU</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 08:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1225.png" alt="EGY" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">EGY</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1225.png" alt="EGY" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">EGY</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SAT, 27 JUN, 2026, 08:30 IST</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1804.png" alt="NZL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NZL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1804.png" alt="NZL" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">NZL</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>
            <a  class="snap-start shrink-0 w-[260px] sm:w-[280px] glass-card rounded-2xl p-4 flex flex-col gap-4 group hover:border-electric transition-colors border border-navy-800 bg-navy-900/60 shadow-lg">
                <div class="flex justify-between items-center text-[11px] font-semibold tracking-wider uppercase text-text-muted">
                    <span>Upcoming</span>
                    <span class="text-text-muted">SUN, 28 JUN, 2026</span>
                </div>
                
                <div class="flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1869.png" alt="PAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img src="https://sportstar.thehindu.com/theme/images/ss-online/matchcenter/flags/football/t1869.png" alt="PAN" class="w-7 h-5 rounded-sm object-cover border border-navy-700">
                            <span class="text-text-primary font-bold text-sm">PAN</span>
                        </div>
                        <span class="text-lg font-bold text-text-primary">-</span>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-7 bg-electric rounded-full"></div>
                <h2 class="font-heading font-bold text-2xl text-text-primary">Editor's Picks</h2>
            </div>
            <div class="space-y-5">
                <?php $__empty_1 = true; $__currentLoopData = $editorPicks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php /** @var \App\Models\Post $post */ ?>
                    <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="flex gap-4 glass-card rounded-xl p-4 group cursor-pointer hover:bg-navy-800/60 transition-colors">
                        <div class="shrink-0 w-28 h-24 sm:w-36 sm:h-28 rounded-lg overflow-hidden bg-navy-800">
                            <img loading="lazy" src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex flex-col justify-center min-w-0">
                            <?php if($post->category): ?>
                            <span class="text-xs font-bold uppercase tracking-wider text-electric mb-1"><?php echo e($post->category?->name); ?></span>
                            <?php endif; ?>
                            <h3 class="font-heading font-semibold text-base text-text-primary leading-snug group-hover:text-electric-light transition-colors line-clamp-2"><?php echo e($post->title); ?></h3>
                            <p class="text-text-secondary text-sm leading-relaxed mt-1 line-clamp-2 hidden sm:block"><?php echo e($post->excerpt); ?></p>
                            <div class="text-xs text-text-muted mt-2">
                                <?php echo e($post->author?->name ?? 'Staff Writer'); ?> · <?php echo e($post?->reading_time ?? 5); ?> min read · <?php echo e($post->published_at?->diffForHumans() ?? ''); ?>

                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-text-muted text-sm">No editor picks yet.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <aside class="space-y-6">
            <?php $ad_sidebar = \App\Models\Setting::get('ad_sidebar', ''); ?>
            <?php if($ad_sidebar): ?>
            <div class="glass-card rounded-xl p-4 flex justify-center items-center overflow-hidden">
                <?php echo $ad_sidebar; ?>

            </div>
            <?php endif; ?>

            
            <div class="glass-card rounded-xl p-6">
                <h3 class="font-heading font-bold text-lg text-text-primary mb-2">📬 Stay Informed</h3>
                <p class="text-text-secondary text-sm mb-4">Get the top stories delivered to your inbox every morning.</p>
                <form action="<?php echo e(route('subscribe')); ?>" method="POST" class="space-y-3" id="sidebar-subscribe-form">
                    <?php echo csrf_field(); ?>
                    <input type="email" name="email" autocomplete="email" placeholder="your@email.com" required class="w-full px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted text-sm focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">Subscribe Free</button>
                </form>
            </div>

            
            <?php echo $__env->make('partials.donate-widget', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="glass-card rounded-xl p-6 text-center">
                <h3 class="font-heading font-bold text-lg text-text-primary mb-2">🧭 Explore Topics</h3>
                <p class="text-text-secondary text-sm mb-4">Discover the latest stories by categories and tags.</p>
                <a href="<?php echo e(route('explore')); ?>" class="w-full inline-block px-4 py-2.5 rounded-lg bg-navy-800 border border-navy-700 text-text-primary text-sm font-semibold hover:bg-electric hover:text-white hover:border-electric transition-colors shadow-sm">
                    Browse All Topics
                </a>
            </div>
        </aside>
    </div>
</section>


<?php if(isset($categorySections) && $categorySections->count() > 0): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-12">
    <?php $__currentLoopData = $categorySections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($cat->posts->count() > 0): ?>
    <div class="border-b border-navy-700/50 pb-12 last:border-0 last:pb-0">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="font-heading font-bold text-xl text-text-primary uppercase tracking-wide flex items-center gap-2">
                <a href="<?php echo e(route('category', $cat->slug)); ?>" class="hover:text-electric transition-colors"><?php echo e($cat->name); ?></a>
                <svg class="w-5 h-5 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <?php $__currentLoopData = $cat->posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="block group flex flex-col">
                <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-3">
                    <?php if($post->featured_image): ?>
                        <img src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <?php else: ?>
                        <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 to-transparent opacity-50 group-hover:opacity-80 transition-opacity"></div>
                </div>
                <h3 class="font-heading font-semibold text-sm text-text-primary group-hover:text-electric transition-colors line-clamp-3">
                    <?php echo e($post->title); ?>

                </h3>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>


<?php if(isset($shortVideos) && $shortVideos->count() > 0): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="font-heading font-bold text-xl text-text-primary uppercase tracking-wide">Short Videos</h2>
    </div>
    
    <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-6 scroll-hide -mx-4 px-4 sm:mx-0 sm:px-0">
        <?php $__currentLoopData = $shortVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('frontend.article', $post->slug)); ?>" class="snap-start shrink-0 w-[160px] block group">
            <div class="relative aspect-[9/16] rounded-xl overflow-hidden mb-3 border border-navy-700/50 group-hover:border-electric/50 transition-colors">
                <?php if($post->featured_image): ?>
                    <img src="<?php echo e($post->featuredImageUrl()); ?>" alt="<?php echo e($post->title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <?php else: ?>
                    <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-900/20 to-transparent"></div>
                <div class="absolute bottom-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1 shadow-lg">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    SHORT
                </div>
            </div>
            <h3 class="font-heading font-semibold text-sm text-text-primary group-hover:text-electric transition-colors line-clamp-2">
                <?php echo e($post->title); ?>

            </h3>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>


<?php if(isset($viralVideos) && $viralVideos->count() > 0): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="font-heading font-bold text-xl text-text-primary uppercase tracking-wide">Top Viral Videos</h2>
    </div>
    
    <div class="bg-navy-900/30 rounded-3xl p-6 border border-navy-800/50">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php $firstVideo = $viralVideos->first(); ?>
            
            <button type="button" @click="$dispatch('open-video-modal', { url: '<?php echo e($firstVideo->redirect_url ?? ''); ?>', content: `<?php echo e(addslashes($firstVideo->content)); ?>` })" class="block group text-left w-full">
                <div class="relative aspect-video rounded-2xl overflow-hidden mb-4 border border-navy-700/50">
                    <?php if($firstVideo->featured_image): ?>
                        <img src="<?php echo e(Storage::url($firstVideo->featured_image)); ?>" alt="<?php echo e($firstVideo->title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <?php else: ?>
                        <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-[0_0_20px_rgba(220,38,38,0.5)] group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-transparent to-transparent pointer-events-none"></div>
                </div>
                <h3 class="font-heading font-bold text-2xl text-text-primary group-hover:text-electric transition-colors line-clamp-2">
                    <?php echo e($firstVideo->title); ?>

                </h3>
                <p class="text-text-secondary mt-2 line-clamp-3 text-sm"><?php echo e($firstVideo->excerpt); ?></p>
            </button>

            
            <?php if($viralVideos->count() > 1): ?>
            <div class="flex flex-col gap-4">
                <?php $__currentLoopData = $viralVideos->skip(1)->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" @click="$dispatch('open-video-modal', { url: '<?php echo e($video->redirect_url ?? ''); ?>', content: `<?php echo e(addslashes($video->content)); ?>` })" class="group flex gap-4 items-center text-left w-full hover:bg-navy-800/40 p-2 rounded-xl transition-colors">
                    <div class="w-32 sm:w-40 aspect-video shrink-0 rounded-lg overflow-hidden relative border border-navy-700/50">
                        <?php if($video->featured_image): ?>
                            <img src="<?php echo e(Storage::url($video->featured_image)); ?>" alt="<?php echo e($video->title); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                            <img src="<?php echo e(asset('images/atomni-placeholder.svg')); ?>" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors flex items-center justify-center">
                            <div class="w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-heading font-semibold text-sm sm:text-base text-text-primary group-hover:text-electric transition-colors line-clamp-3">
                            <?php echo e($video->title); ?>

                        </h4>
                    </div>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<div x-data="{ open: false, url: '', videoId: '', extractYouTubeId(url) {
        let match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^#\&\?]*).*/);
        return (match && match[1].length == 11) ? match[1] : null;
    } }"
     x-on:open-video-modal.window="
        url = $event.detail.url;
        let id = extractYouTubeId(url);
        if(!id && $event.detail.content) {
            id = extractYouTubeId($event.detail.content);
        }
        if(id) {
            videoId = id;
            open = true;
        } else if(url) {
            window.open(url, '_blank');
        }
     "
     x-show="open" 
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="absolute inset-0 bg-navy-950/95 backdrop-blur-sm" @click="open = false; videoId = '';"></div>
    
    <div class="relative w-full max-w-5xl bg-navy-900 rounded-2xl shadow-2xl overflow-hidden border border-navy-700/50"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="flex items-center justify-between p-4 border-b border-navy-800">
            <h3 class="font-heading font-bold text-text-primary">Watch Video</h3>
            <div class="flex gap-2">
                <a :href="'https://www.youtube.com/watch?v=' + videoId" target="_blank" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
                    Watch on YouTube
                </a>
                <button @click="open = false; videoId = '';" class="p-1.5 bg-navy-800 hover:bg-navy-700 text-text-secondary hover:text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        
        <div class="aspect-video w-full bg-black">
            <template x-if="videoId">
                <iframe class="w-full h-full" :src="'https://www.youtube.com/embed/' + videoId + '?autoplay=1'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </template>
        </div>
    </div>
</div>
<?php endif; ?>



<section id="newsletter" class="bg-navy-900/80 border-y border-navy-700/30 py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="font-heading font-bold text-3xl text-text-primary mb-3">Never Miss a Story</h2>
        <p class="text-text-secondary text-base mb-8">Join 50,000+ readers who get the most important news delivered straight to their inbox, every day.</p>
        <form action="<?php echo e(route('subscribe')); ?>" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" id="footer-subscribe-form">
            <?php echo csrf_field(); ?>
            <input type="email" name="email" autocomplete="email" placeholder="Enter your email address" required class="flex-1 px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-text-primary placeholder:text-text-muted focus:outline-none focus:border-electric focus:ring-2 focus:ring-electric/30 transition-all">
            <button type="submit" class="px-6 py-3 rounded-xl bg-electric hover:bg-electric-light text-white font-semibold transition-all shadow-lg shadow-electric/20">Subscribe</button>
        </form>
        <p class="text-text-muted text-xs mt-4">No spam, ever. Unsubscribe anytime.</p>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/home.blade.php ENDPATH**/ ?>