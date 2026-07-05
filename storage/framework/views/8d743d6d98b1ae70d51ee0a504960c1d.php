
<footer class="border-t border-navy-700/30 bg-navy-900/50">
    <?php
        $socialLinks = [
            ['key' => 'social_twitter', 'label' => 'Twitter', 'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
            ['key' => 'social_facebook', 'label' => 'Facebook', 'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'],
            ['key' => 'social_instagram', 'label' => 'Instagram', 'icon' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>'],
            ['key' => 'social_linkedin', 'label' => 'LinkedIn', 'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'],
            ['key' => 'social_youtube', 'label' => 'YouTube', 'icon' => '<path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>'],
            ['key' => 'social_rss', 'label' => 'RSS', 'icon' => ''],
        ];
        $siteName = \App\Models\Setting::get('site_name', 'Atomni');
        $logoLight = \App\Models\Setting::get('site_logo');
        $logoDark = \App\Models\Setting::get('site_logo_dark');
    ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-8 border-b border-navy-700/30 light:border-slate-200">
            
            
            <div class="flex flex-col md:flex-row md:items-center gap-6 lg:gap-12 w-full lg:w-auto">
                
                <a href="<?php echo e(route('home')); ?>" class="shrink-0 focus:outline-none focus:ring-2 focus:ring-electric rounded block">
                    <?php if($logoLight && $logoDark): ?>
                        <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoLight)); ?>" alt="<?php echo e($siteName); ?>" class="h-9 w-auto object-contain logo-light">
                        <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoDark)); ?>" alt="<?php echo e($siteName); ?>" class="h-9 w-auto object-contain logo-dark">
                    <?php elseif($logoLight || $logoDark): ?>
                        <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoLight ?: $logoDark)); ?>" alt="<?php echo e($siteName); ?>" class="h-9 w-auto object-contain">
                    <?php else: ?>
                        <img loading="lazy" src="<?php echo e(asset('images/atomni-logo-light.svg')); ?>" alt="<?php echo e($siteName); ?>" class="h-9 w-auto object-contain logo-light">
                        <img loading="lazy" src="<?php echo e(asset('images/atomni-logo-dark.svg')); ?>" alt="<?php echo e($siteName); ?>" class="h-9 w-auto object-contain logo-dark">
                    <?php endif; ?>
                </a>
                
                
                <div class="flex-1 w-full max-w-md">
                    <p class="text-[13px] font-bold text-text-primary light:text-slate-800 mb-2">Subscribe to our best newsletters</p>
                    <form action="<?php echo e(route('subscribe')); ?>" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-0">
                        <?php echo csrf_field(); ?>
                        <div class="relative flex-1">
                            <input type="email" name="email" autocomplete="email" placeholder="Enter your email address" required 
                                   class="w-full bg-navy-800/80 light:bg-white text-[13px] text-text-primary light:text-slate-800 px-4 py-2 border border-navy-600 light:border-slate-300 rounded sm:rounded-r-none focus:outline-none focus:border-electric focus:ring-1 focus:ring-electric transition-colors">
                        </div>
                        <button type="submit" 
                                class="bg-navy-800 light:bg-slate-800 text-text-primary light:text-white hover:bg-electric light:hover:bg-electric text-[12px] font-bold px-5 py-2 rounded sm:rounded-l-none border border-navy-600 sm:border-l-0 light:border-slate-800 hover:border-electric light:hover:border-electric transition-colors uppercase tracking-wider whitespace-nowrap">
                            Sign Up
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="flex items-center gap-3 md:justify-end shrink-0">
                <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $url = \App\Models\Setting::get($social['key'], ''); 
                        $href = $url ? $url : '#';
                    ?>
                    <?php if($url || !app()->environment('production')): ?>
                        <a href="<?php echo e($href); ?>" target="<?php echo e($url ? '_blank' : '_self'); ?>" rel="noopener noreferrer" 
                           class="w-9 h-9 rounded-full bg-navy-800 light:bg-slate-200 flex items-center justify-center text-text-secondary light:text-slate-600 hover:bg-electric light:hover:bg-electric hover:text-white light:hover:text-white focus:outline-none focus:ring-2 focus:ring-electric focus:ring-offset-2 focus:ring-offset-navy-900 light:focus:ring-offset-white transition-all duration-200 opacity-<?php echo e($url ? '100' : '50'); ?>" 
                           aria-label="<?php echo e($social['label']); ?>">
                            <?php if($social['key'] === 'social_rss'): ?>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            <?php else: ?>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><?php echo $social['icon']; ?></svg>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="py-10 border-b border-navy-700/30 light:border-slate-200">
            <?php
                // Collect all available footer menus
                $footerColumns = collect([
                    $footerCompany ?? null,
                    $footerCategories ?? null,
                    $footerLegal ?? null,
                    $footerResources ?? null,
                ])->filter();
                
                // Fallback if no menus exist in DB
                $hasDynamicMenus = $footerColumns->count() > 0;
            ?>

            <?php if($hasDynamicMenus): ?>
                <?php $catCount = $footerCategories ? optional($footerCategories->rootItems)->count() : 0; ?>
                <div class="grid grid-cols-1 md:grid-cols-<?php echo e(min($footerColumns->count() + ($catCount > 6 ? 1 : 0), 4)); ?> gap-8">
                    <?php $__currentLoopData = $footerColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $footerMenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($footerMenu): ?>
                            <div class="<?php echo e($footerMenu->location === 'footer_categories' && optional($footerMenu->rootItems)->count() > 6 ? 'md:col-span-2' : ''); ?>">
                                <h3 class="text-[13px] font-bold text-text-primary light:text-slate-900 mb-4 uppercase tracking-wider">
                                    <?php echo e(str_replace(['Footer — ', 'Footer - '], '', $footerMenu?->name ?? '')); ?>

                                </h3>
                                <?php if($footerMenu->location === 'footer_categories' && optional($footerMenu->rootItems)->count() > 6): ?>
                                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2.5">
                                        <?php $__currentLoopData = $footerMenu?->rootItems ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo e(url(optional($item)?->url ?? '#')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">
                                                    <?php echo e(optional($item)?->title ?? ''); ?>

                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <ul class="space-y-2.5">
                                        <?php $__currentLoopData = $footerMenu?->rootItems ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo e(url(optional($item)?->url ?? '#')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">
                                                    <?php echo e(optional($item)?->title ?? ''); ?>

                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-[13px] font-bold text-text-primary light:text-slate-900 mb-4 uppercase tracking-wider">Company</h3>
                        <ul class="space-y-2.5">
                            <li><a href="<?php echo e(route('about')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">About Us</a></li>
                            <li><a href="<?php echo e(route('contact')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Contact Us</a></li>
                            <li><a href="<?php echo e(route('careers.index')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Careers</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-bold text-text-primary light:text-slate-900 mb-4 uppercase tracking-wider">Resources</h3>
                        <ul class="space-y-2.5">
                            <li><a href="<?php echo e(route('advertise')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Advertise</a></li>
                            <li><a href="<?php echo e(route('press-kit')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Press Kit</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-bold text-text-primary light:text-slate-900 mb-4 uppercase tracking-wider">Legal</h3>
                        <ul class="space-y-2.5">
                            <li><a href="<?php echo e(route('privacy')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Privacy Policy</a></li>
                            <li><a href="<?php echo e(route('terms')); ?>" class="text-[13px] font-medium text-text-muted light:text-slate-600 hover:text-electric light:hover:text-electric transition-colors">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <p class="text-text-muted light:text-slate-500 text-xs shrink-0">Copyright © <?php echo e(date('Y')); ?> <?php echo e($siteName ?? 'Atomni'); ?>. All Rights Reserved.</p>
            <p class="text-text-muted light:text-slate-500 text-xs shrink-0 italic max-w-lg hidden sm:block">
                <?php echo e(\App\Models\Setting::get('website_tagline', 'Your trusted source for breaking news and in-depth analysis.')); ?>

            </p>
        </div>
    </div>
</footer>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/partials/footer.blade.php ENDPATH**/ ?>