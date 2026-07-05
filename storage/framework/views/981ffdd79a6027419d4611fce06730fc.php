
<aside id="admin-sidebar" class="fixed inset-y-0 z-40 w-64 bg-navy-900 border-r border-navy-700/50" style="left: -16rem; transition: left 0.3s ease;">
    <div class="flex flex-col h-full">
        
        <div class="flex items-center gap-2 px-6 h-16 border-b border-navy-700/50 shrink-0">
            <?php 
                $logoLight = \App\Models\Setting::get('site_logo');
                $logoDark = \App\Models\Setting::get('site_logo_dark');
                $siteName = \App\Models\Setting::get('site_name', 'Atomni');
            ?>
            <?php if($logoLight && $logoDark): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center focus:outline-none focus:ring-2 focus:ring-electric rounded">
                    <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoLight)); ?>" alt="<?php echo e($siteName); ?>" class="h-8 max-w-[120px] object-contain logo-light">
                    <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoDark)); ?>" alt="<?php echo e($siteName); ?>" class="h-8 max-w-[120px] object-contain logo-dark">
                </a>
            <?php elseif($logoLight || $logoDark): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center focus:outline-none focus:ring-2 focus:ring-electric rounded">
                    <img loading="lazy" src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoLight ?: $logoDark)); ?>" alt="<?php echo e($siteName); ?>" class="h-8 max-w-[120px] object-contain">
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center focus:outline-none focus:ring-2 focus:ring-electric rounded">
                    <img loading="lazy" src="<?php echo e(asset('images/atomni-logo-light.svg')); ?>" alt="<?php echo e($siteName); ?>" class="h-8 max-w-[120px] object-contain logo-light">
                    <img loading="lazy" src="<?php echo e(asset('images/atomni-logo-dark.svg')); ?>" alt="<?php echo e($siteName); ?>" class="h-8 max-w-[120px] object-contain logo-dark">
                </a>
            <?php endif; ?>
            <span class="ml-auto text-[10px] font-medium px-1.5 py-0.5 rounded bg-electric/15 text-electric uppercase tracking-wider">Admin</span>
        </div>

        
        <?php
            $draftPostsCount = \App\Models\Post::where('status', 'draft')->count();
            $draftPagesCount = \App\Models\Page::where('is_published', false)->count();
            $pendingComments = \App\Models\Comment::where('is_approved', false)->count();
            $newContactCount = \App\Models\ContactQuery::where('status', 'new')->count();
            $newAppsCount = \App\Models\JobApplication::where('status', 'new')->count();
            $activeJobsCount = \App\Models\JobPosting::where('status', 'published')->count();
            $subscriberCount = \App\Models\Newsletter::count();
        ?>

        
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Main</p>

            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.dashboard') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            
            <?php if(in_array(auth()->user()->role, ['super_admin', 'editor'])): ?>
            <a href="<?php echo e(route('admin.reports')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.reports') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Reports
            </a>
            <?php endif; ?>

            <p class="px-3 mt-5 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Content</p>

            <a href="<?php echo e(route('admin.posts.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.posts.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Posts
                <?php if($draftPostsCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400"><?php echo e($draftPostsCount); ?></span>
                <?php endif; ?>
            </a>

            
            <?php if(in_array(auth()->user()->role, ['super_admin', 'editor'])): ?>
            <a href="<?php echo e(route('admin.pages.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.pages.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                Pages
                <?php if($draftPagesCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400"><?php echo e($draftPagesCount); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            
            <?php if(in_array(auth()->user()->role, ['super_admin', 'editor'])): ?>
            <a href="<?php echo e(route('admin.team-members.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.team-members.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V18a3 3 0 00-5-2.2zM9 18v2m4-2v2m-4-2H5a3 3 0 00-5 2v2h14v-2a3 3 0 00-5-2zm4-6a4 4 0 11-8 0 4 4 0 018 0zm10 0a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Team Members
            </a>
            <?php endif; ?>

            
            <?php if(in_array(auth()->user()->role, ['super_admin', 'editor'])): ?>
            <a href="<?php echo e(route('admin.categories.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.categories.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Categories
            </a>
            
            <a href="<?php echo e(route('admin.homepage-sections.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.homepage-sections.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                Homepage Sections
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.media.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.media.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Media Library
            </a>

            <p class="px-3 mt-5 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Careers</p>

            
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <a href="<?php echo e(route('admin.careers.jobs.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.careers.jobs.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Postings
                <?php if($activeJobsCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400"><?php echo e($activeJobsCount); ?></span>
                <?php endif; ?>
            </a>

            <a href="<?php echo e(route('admin.careers.applications.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.careers.applications.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Applications
                <?php if($newAppsCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-alert-red/20 text-alert-red"><?php echo e($newAppsCount); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <p class="px-3 mt-5 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Engagement</p>

            
            <?php if(in_array(auth()->user()->role, ['super_admin', 'editor'])): ?>
            <a href="<?php echo e(route('admin.comments.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.comments.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Comments
                <?php if($pendingComments > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-alert-red/20 text-alert-red"><?php echo e($pendingComments); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <a href="<?php echo e(route('admin.contacts.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.contacts.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Contact Queries
                <?php if($newContactCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-alert-red/20 text-alert-red"><?php echo e($newContactCount); ?></span>
                <?php endif; ?>
            </a>

            <a href="<?php echo e(route('admin.newsletter.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.newsletter.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m10 0a3 3 0 01-6 0m6 0H9"/></svg>
                Newsletter
                <?php if($subscriberCount > 0): ?>
                    <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-electric/20 text-electric"><?php echo e($subscriberCount); ?></span>
                <?php endif; ?>
            </a>

            <a href="<?php echo e(route('admin.donors.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.donors.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                Donor Shoutouts
            </a>
            <?php endif; ?>

            <p class="px-3 mt-5 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Settings</p>

            
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <a href="<?php echo e(route('admin.settings.global')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.global') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Global Settings
            </a>
            
            <a href="<?php echo e(route('admin.menus.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.menus.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                Menus
            </a>

            <a href="<?php echo e(route('admin.settings.integrations')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.integrations') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                Integrations
            </a>

            <a href="<?php echo e(route('admin.settings.social')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.social') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                Social Links
            </a>

            <a href="<?php echo e(route('admin.settings.ads')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.ads') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Ad Controls
            </a>

            <a href="<?php echo e(route('admin.settings.permalink')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.permalink') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Permalinks
            </a>

            <a href="<?php echo e(route('admin.settings.rss')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.settings.rss') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                RSS Feeds
            </a>
            <?php endif; ?>

            <?php if(auth()->user()->isSuperAdmin()): ?>
            <a href="<?php echo e(route('admin.users.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.users.*') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Team Logins
            </a>
            <?php endif; ?>

            
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <p class="px-3 mt-5 mb-2 text-[10px] font-semibold tracking-widest uppercase text-text-muted">Tools</p>

            <a href="<?php echo e(route('admin.tools.index')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.tools.index') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Tools Overview
            </a>

            <a href="<?php echo e(route('admin.tools.site-health')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.tools.site-health') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Site Health
            </a>

            <a href="<?php echo e(route('admin.tools.import-export')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.tools.import-export') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import / Export
            </a>

            <a href="<?php echo e(route('admin.tools.cache')); ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request()->routeIs('admin.tools.cache') ? 'text-electric bg-electric/10' : 'text-text-secondary hover:text-text-primary hover:bg-navy-800/60'); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Cache Manager
            </a>
            <?php endif; ?>
        </nav>

        
        <div class="px-4 py-3 border-t border-navy-700/50 shrink-0">
            <a href="/" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-text-muted hover:text-text-primary hover:bg-navy-800/60 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Back to Site
            </a>
        </div>
    </div>
</aside>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/admin/partials/sidebar.blade.php ENDPATH**/ ?>