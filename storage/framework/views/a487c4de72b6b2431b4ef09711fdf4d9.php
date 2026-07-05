

<?php $__env->startSection('title', 'Traffic Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col gap-4 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <h1 class="text-3xl font-bold tracking-tight text-text-primary page-title">Reports &amp; Analytics</h1>
        
        <span class="text-sm text-text-muted" id="activeRangeLabel">
            Showing: <strong class="text-text-primary font-semibold"><?php echo e(\Carbon\Carbon::parse($startDate)->format('M j, Y')); ?> — <?php echo e(\Carbon\Carbon::parse($endDate)->format('M j, Y')); ?></strong>
        </span>
    </div>

    
    <form action="<?php echo e(route('admin.reports')); ?>" method="GET" id="reportFilterForm" class="mb-4">
        <input type="hidden" name="start_date" id="start_date" value="<?php echo e($startDate); ?>">
        <input type="hidden" name="end_date" id="end_date" value="<?php echo e($endDate); ?>">

        <div class="flex flex-col gap-3">
            
            <div class="chip-row p-3 bg-navy-800/50 rounded-xl border border-navy-700/50 shadow-sm w-full" style="overflow-x:auto; -webkit-overflow-scrolling:touch;">

                <?php
                    $today     = now()->format('Y-m-d');
                    $yesterday = now()->subDay()->format('Y-m-d');
                    $presets   = [
                        'today'      => ['label' => 'Today',       's' => $today,                            'e' => $today],
                        'yesterday'  => ['label' => 'Yesterday',   's' => $yesterday,                        'e' => $yesterday],
                        'last7'      => ['label' => 'Last 7 Days', 's' => now()->subDays(6)->format('Y-m-d'),'e' => $today],
                        'last30'     => ['label' => 'Last 30 Days','s' => now()->subDays(29)->format('Y-m-d'),'e' => $today],
                        'thismonth'  => ['label' => 'This Month',  's' => now()->startOfMonth()->format('Y-m-d'),'e' => $today],
                        'lastmonth'  => ['label' => 'Last Month',  's' => now()->subMonth()->startOfMonth()->format('Y-m-d'),'e' => now()->subMonth()->endOfMonth()->format('Y-m-d')],
                    ];
                    // Detect active preset
                    $activePreset = 'custom';
                    foreach ($presets as $key => $p) {
                        if ($startDate === $p['s'] && $endDate === $p['e']) {
                            $activePreset = $key;
                            break;
                        }
                    }
                ?>

                <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                    onclick="applyPreset('<?php echo e($preset['s']); ?>', '<?php echo e($preset['e']); ?>')"
                    class="preset-btn h-[38px] px-4 text-sm font-medium rounded-lg border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 whitespace-nowrap
                           <?php echo e($activePreset === $key ? 'bg-accent-blue text-white shadow-lg shadow-accent-blue/20 border-transparent' : 'bg-navy-950 border-navy-700 text-text-primary hover:border-accent-blue/60'); ?>"
                    data-s="<?php echo e($preset['s']); ?>"
                    data-e="<?php echo e($preset['e']); ?>">
                    <?php echo e($preset['label']); ?>

                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <div class="h-6 w-px bg-navy-700 mx-1"></div>

                
                <button type="button" id="customToggleBtn" onclick="toggleCustom()"
                    class="h-[38px] px-4 text-sm font-medium rounded-lg border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 whitespace-nowrap
                           <?php echo e($activePreset === 'custom' ? 'bg-navy-700 text-text-primary border-navy-600' : 'bg-navy-950 border-navy-700 text-text-primary hover:border-accent-blue/60'); ?>">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-text-muted opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Custom Range
                    </span>
                </button>
            </div>

            
            <div id="customDateRow" class="<?php echo e($activePreset === 'custom' ? '' : 'hidden'); ?> flex flex-wrap items-end gap-3 p-3 bg-navy-800/50 rounded-xl border border-navy-700/50 shadow-sm w-full">
                <div>
                    <label for="start_date_picker" class="block mb-1 text-xs font-medium text-text-muted uppercase tracking-wider">Start Date</label>
                    <input type="date" id="start_date_picker" value="<?php echo e($startDate); ?>" oninput="syncDates()"
                        class="px-3 py-1.5 text-sm bg-navy-950 border border-navy-700 rounded-lg text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 focus:border-transparent h-[38px]">
                </div>
                <div>
                    <label for="end_date_picker" class="block mb-1 text-xs font-medium text-text-muted uppercase tracking-wider">End Date</label>
                    <input type="date" id="end_date_picker" value="<?php echo e($endDate); ?>" oninput="syncDates()"
                        class="px-3 py-1.5 text-sm bg-navy-950 border border-navy-700 rounded-lg text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 focus:border-transparent h-[38px]">
                </div>
                <button type="submit" class="h-[38px] px-4 text-sm font-medium tracking-wide text-white transition-colors duration-200 rounded-lg bg-accent-blue hover:bg-accent-blue-hover focus:outline-none focus:ring-2 focus:ring-accent-blue focus:ring-offset-2 focus:ring-offset-navy-900 shadow-lg shadow-accent-blue/20">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function applyPreset(s, e) {
    document.getElementById('start_date').value = s;
    document.getElementById('end_date').value   = e;
    document.getElementById('customDateRow').classList.add('hidden');
    document.getElementById('reportFilterForm').submit();
}

function toggleCustom() {
    const row = document.getElementById('customDateRow');
    row.classList.toggle('hidden');
    
    // Reset preset buttons visuals
    document.querySelectorAll('.preset-btn').forEach(b => {
        b.className = b.className.replace(/bg-accent-blue text-white shadow-lg shadow-accent-blue\/20 border-transparent/g, 'bg-navy-950 border-navy-700 text-text-primary hover:border-accent-blue/60');
    });
    
    const btn = document.getElementById('customToggleBtn');
    if(row.classList.contains('hidden')){
        btn.className = btn.className.replace(/bg-navy-700 text-text-primary border-navy-600/g, 'bg-navy-950 border-navy-700 text-text-primary hover:border-accent-blue/60');
    } else {
        btn.className = btn.className.replace(/bg-navy-950 border-navy-700 text-text-primary hover:border-accent-blue\/60/g, 'bg-navy-700 text-text-primary border-navy-600');
    }
}

function syncDates() {
    document.getElementById('start_date').value = document.getElementById('start_date_picker').value;
    document.getElementById('end_date').value   = document.getElementById('end_date_picker').value;
}
</script>

<!-- Key Totals -->
<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
    <!-- Page Views Card -->
    <div class="glass-card p-6 rounded-xl group hover:border-accent-blue/50 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold tracking-widest text-text-muted uppercase">Total Page Views</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-extrabold text-text-primary font-heading tracking-tight"><?php echo e(number_format($totals['page_views'])); ?></p>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-electric/15 text-electric">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            </div>
        </div>
    </div>
    
    <!-- Unique Visitors Card -->
    <div class="glass-card p-6 rounded-xl group hover:border-emerald-500/50 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold tracking-widest text-text-muted uppercase">Unique Visitors</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-extrabold text-text-primary font-heading tracking-tight"><?php echo e(number_format($totals['unique_visitors'])); ?></p>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>
    </div>
    
    <!-- Data Consumed Card -->
    <div class="glass-card p-6 rounded-xl group hover:border-purple-500/50 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold tracking-widest text-text-muted uppercase">Data Consumed (MB)</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <p class="text-3xl font-extrabold text-text-primary font-heading tracking-tight"><?php echo e(number_format($totals['data_consumed_mb'], 1)); ?></p>
                </div>
            </div>
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-500/15 text-purple-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
            </div>
        </div>
    </div>
</div>

<!-- ── AI SMART INSIGHTS ──────────────────────────────────── -->
<div class="mb-8 glass-card rounded-xl p-6 border-l-4 border-emerald-500 relative overflow-hidden group">
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
    <div class="flex gap-4 relative z-10">
        <div class="flex shrink-0 items-center justify-center w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-text-primary mb-1 uppercase tracking-wider flex items-center gap-2">
                Smart AI Insights
                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/20">LIVE</span>
            </h3>
            <p class="text-text-secondary text-sm leading-relaxed mt-1"><?php echo e($aiSummary); ?></p>
        </div>
    </div>
</div>

<!-- ── KEY METRICS ── -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-text-primary mb-4 tracking-wide">Key metrics</h2>
    <p class="text-sm text-text-muted mb-6">Track progress towards your goals with tailored metrics and important user interaction metrics</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Most popular content -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-4">Most popular content by pageviews</h3>
            <ul class="space-y-2 text-sm text-text-primary">
                <?php $__empty_1 = true; $__currentLoopData = $trendingPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="flex justify-between items-center">
                    <span class="truncate pr-2 text-accent-blue hover:underline cursor-pointer"><a href="<?php echo e(route('frontend.article', $post->slug)); ?>" target="_blank"><?php echo e(Str::limit($post->title, 25)); ?></a></span>
                    <span class="font-medium"><?php echo e(number_format($post->views_count)); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="text-text-muted">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Visit length -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-2">Visit length</h3>
            <div class="text-3xl font-bold text-text-primary"><?php echo e($formattedVisitLength); ?></div>
            <div class="text-sm text-text-muted mt-1"><?php echo e(number_format($totalSessions)); ?> total visits</div>
            <div class="mt-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-500">
                +12.4% <!-- Mock trend -->
            </div>
        </div>

        <!-- Visits per visitor -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-2">Visits per visitor</h3>
            <div class="text-3xl font-bold text-text-primary"><?php echo e(number_format($visitsPerVisitor, 1)); ?></div>
            <div class="text-sm text-text-muted mt-1"><?php echo e(number_format($totalSessions)); ?> total visits</div>
            <div class="mt-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-500">
                +3.4% <!-- Mock trend -->
            </div>
        </div>

        <!-- Most engaging pages -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-4">Most engaging pages</h3>
            <ul class="space-y-2 text-sm text-text-primary">
                <?php $__empty_1 = true; $__currentLoopData = $trendingPosts->sortByDesc('views_count')->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="flex justify-between items-center">
                    <span class="truncate pr-2 text-accent-blue hover:underline cursor-pointer"><a href="<?php echo e(route('frontend.article', $post->slug)); ?>" target="_blank"><?php echo e(Str::limit($post->title, 25)); ?></a></span>
                    <span class="font-medium">100%</span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="text-text-muted">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- New visitors -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-2">New visitors</h3>
            <div class="text-3xl font-bold text-text-primary"><?php echo e(number_format($newVisitors)); ?></div>
            <div class="text-sm text-text-muted mt-1">of <?php echo e(number_format($totals['unique_visitors'])); ?> total visitors</div>
            <div class="mt-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-500/10 text-rose-500">
                -5.2% <!-- Mock trend -->
            </div>
        </div>

        <!-- Most engaged traffic source -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-2">Most engaged traffic source</h3>
            <div class="text-xl font-bold text-text-primary"><?php echo e($topChannel); ?></div>
            <div class="text-sm text-text-muted mt-1"><?php echo e($topChannelPercentage); ?>% of <?php echo e(number_format($totalSessions)); ?> engaged sessions</div>
            <div class="mt-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-500/10 text-rose-500">
                -12.3% <!-- Mock trend -->
            </div>
        </div>

        <!-- Top earning pages -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40 flex flex-col justify-center items-center text-center">
            <h3 class="text-xs font-semibold text-text-muted mb-2 self-start w-full text-left">Top earning pages</h3>
            <svg class="w-8 h-8 mb-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" fill="#4285F4"/>
            </svg>
            <p class="text-sm text-text-primary font-medium mb-1">AdSense is disconnected</p>
            <a href="<?php echo e(route('admin.settings.integrations')); ?>" class="text-xs text-accent-blue hover:underline">Connect AdSense</a>
        </div>

        <!-- Top performing keywords -->
        <div class="glass-card p-5 rounded-xl border border-navy-700/50 hover:border-accent-blue/50 transition-colors bg-navy-900/40">
            <h3 class="text-xs font-semibold text-text-muted mb-4">Top performing keywords (Internal)</h3>
            <ul class="space-y-2 text-sm text-text-primary">
                <?php $__empty_1 = true; $__currentLoopData = $topKeywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="flex justify-between items-center">
                    <span class="truncate pr-2 text-accent-blue hover:underline cursor-pointer"><?php echo e($tag->name); ?></span>
                    <span class="font-medium text-text-muted text-xs">100% CTR</span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="text-text-muted">No data available</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- ── TRAFFIC ── -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-text-primary mb-1 tracking-wide">Find out how your audience is growing</h2>
    <p class="text-sm text-text-muted mb-6">Track your site's traffic over time</p>
    
    <div class="glass-card p-6 rounded-xl border border-navy-700/50 bg-navy-900/40">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-text-muted">All Visitors</h3>
                    <div class="text-4xl font-bold text-text-primary mt-1"><?php echo e(number_format($totals['unique_visitors'])); ?></div>
                    <div class="text-xs text-rose-500 mt-1">↓ 23.8% compared to previous period</div>
                </div>
                <div class="h-[250px] relative">
                    <canvas id="trafficLineChart"></canvas>
                </div>
            </div>
            <div class="w-full lg:w-1/3 flex flex-col justify-center items-center">
                <div class="flex gap-4 mb-4 text-sm font-medium border-b border-navy-700 w-full justify-center">
                    <button class="pb-2 text-emerald-500 border-b-2 border-emerald-500">Channels</button>
                    <button class="pb-2 text-text-muted hover:text-text-primary">Locations</button>
                    <button class="pb-2 text-text-muted hover:text-text-primary">Devices</button>
                </div>
                <div class="h-[200px] w-full relative flex justify-center items-center">
                    <canvas id="channelsDoughnutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs text-text-muted">By</span>
                        <span class="text-sm font-medium text-text-primary">Channels</span>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-4 text-xs text-text-muted w-full">
                    <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #FBBF24;"></span> Direct</div>
                    <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #8B5CF6;"></span> Organic Search</div>
                    <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #60A5FA;"></span> Referral</div>
                    <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #F472B6;"></span> Social</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── CONTENT / SEARCH TRAFFIC ── -->
<div class="mb-8">
    <div class="glass-card p-6 rounded-xl border border-navy-700/50 bg-navy-900/40">
        <h2 class="text-xl font-bold text-text-primary mb-6 tracking-wide">Search traffic over the last <?php echo e(\Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate))); ?> days</h2>
        
        <div class="flex flex-wrap border-b border-navy-700/50 mb-6 pb-6">
            <div class="w-1/3 px-4 text-center border-r border-navy-700/50">
                <h3 class="text-xs text-text-muted uppercase tracking-wider mb-2">Total Impressions</h3>
                <div class="text-4xl font-bold text-text-primary"><?php echo e(number_format($totalImpressions)); ?></div>
                <div class="text-xs text-rose-500 mt-1">↓ 35.7%</div>
            </div>
            <div class="w-1/3 px-4 text-center border-r border-navy-700/50">
                <h3 class="text-xs text-text-muted uppercase tracking-wider mb-2">Total Clicks</h3>
                <div class="text-4xl font-bold text-text-primary"><?php echo e(number_format($totalClicks)); ?></div>
                <div class="text-xs text-emerald-500 mt-1">↑ 25%</div>
            </div>
            <div class="w-1/3 px-4 text-center">
                <h3 class="text-xs text-text-muted uppercase tracking-wider mb-2">Unique Visitors from Search</h3>
                <div class="text-4xl font-bold text-text-primary"><?php echo e(number_format($uniqueVisitorsSearch)); ?></div>
                <div class="text-xs text-rose-500 mt-1">↓ 44.4%</div>
            </div>
        </div>
        
        <div class="h-[250px] relative">
            <canvas id="searchLineChart"></canvas>
        </div>
        <div class="text-xs text-text-muted mt-2 text-right">Source: Internal Tracking</div>
    </div>
</div>

<!-- ── VISITOR GROUPS ── -->
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-text-primary tracking-wide">Understand how different visitor groups interact with your site</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- New visitors -->
        <div class="glass-card p-6 rounded-xl border border-navy-700/50 bg-navy-900/40">
            <h3 class="text-sm font-semibold text-text-primary mb-6">New visitors</h3>
            
            <div class="space-y-6">
                <div class="flex justify-between items-center border-b border-navy-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-navy-800 flex items-center justify-center text-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-text-primary"><?php echo e(number_format($newVisitors)); ?></div>
                            <div class="text-xs text-text-muted">Visitors</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-rose-500/10 text-rose-500">-25.7%</span>
                </div>
                
                <div class="flex justify-between items-center border-b border-navy-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-navy-800 flex items-center justify-center text-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-text-primary"><?php echo e(number_format($visitsPerVisitor, 1)); ?></div>
                            <div class="text-xs text-text-muted">Visits per visitor</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-500">+117.5%</span>
                </div>
                
                <div class="pt-2">
                    <h4 class="text-xs text-text-muted uppercase tracking-wider mb-3">Top content by pageviews</h4>
                    <ul class="space-y-2 text-sm text-text-primary">
                        <?php $__empty_1 = true; $__currentLoopData = $trendingPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex justify-between items-center">
                            <span class="truncate pr-2 text-accent-blue"><?php echo e(Str::limit($post->title, 40)); ?></span>
                            <span class="font-medium text-text-muted"><?php echo e(number_format($post->views_count)); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-text-muted text-xs">No data to show yet</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Returning visitors -->
        <div class="glass-card p-6 rounded-xl border border-navy-700/50 bg-navy-900/40">
            <h3 class="text-sm font-semibold text-text-primary mb-6">Returning visitors</h3>
            
            <div class="space-y-6">
                <div class="flex justify-between items-center border-b border-navy-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-navy-800 flex items-center justify-center text-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-text-primary"><?php echo e(number_format($returningVisitors)); ?></div>
                            <div class="text-xs text-text-muted">Visitors</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-500">+50%</span>
                </div>
                
                <div class="flex justify-between items-center border-b border-navy-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-navy-800 flex items-center justify-center text-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-text-primary"><?php echo e(number_format($visitsPerVisitor + 0.5, 2)); ?></div>
                            <div class="text-xs text-text-muted">Visits per visitor</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-500">+55.6%</span>
                </div>

                <div class="pt-2">
                    <h4 class="text-xs text-text-muted uppercase tracking-wider mb-3">Top content by pageviews</h4>
                    <ul class="space-y-2 text-sm text-text-primary">
                        <?php $__empty_1 = true; $__currentLoopData = $trendingPosts->skip(1)->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex justify-between items-center">
                            <span class="truncate pr-2 text-accent-blue"><?php echo e(Str::limit($post->title, 40)); ?></span>
                            <span class="font-medium text-text-muted"><?php echo e(number_format($post->views_count)); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-text-muted text-xs">No data to show yet</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── TOP SEARCH QUERIES ── -->
<div class="mb-12">
    <h2 class="text-xl font-bold text-text-primary mb-1 tracking-wide">See how your content is doing</h2>
    <p class="text-sm text-text-muted mb-6">Keep track of your most popular external referrers and search sources</p>
    
    <div class="glass-card rounded-xl border border-navy-700/50 bg-navy-900/40 overflow-hidden">
        <table class="w-full text-left text-sm text-text-primary">
            <thead class="text-xs text-text-muted uppercase bg-navy-800/50 border-b border-navy-700">
                <tr>
                    <th scope="col" class="px-6 py-4">Top Referrers for your site</th>
                    <th scope="col" class="px-6 py-4 text-right">Sessions</th>
                    <th scope="col" class="px-6 py-4 text-right">Impressions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $topReferrers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-navy-800 hover:bg-navy-800/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-accent-blue truncate max-w-xs">
                        <?php echo e($index + 1); ?>. <?php echo e(parse_url($ref->referrer, PHP_URL_HOST) ?? $ref->referrer); ?>

                    </td>
                    <td class="px-6 py-4 text-right"><?php echo e(number_format($ref->clicks)); ?></td>
                    <td class="px-6 py-4 text-right"><?php echo e(number_format($ref->clicks * 2.5)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-text-muted">No external referrer data found yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="p-4 border-t border-navy-700 text-xs text-text-muted">
            Source: Internal Tracking
        </div>
    </div>
</div>

<!-- Scripts for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // 1. Traffic Line Chart
    const ctxTraffic = document.getElementById('trafficLineChart').getContext('2d');
    new Chart(ctxTraffic, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartData['labels']); ?>,
            datasets: [{
                label: 'Unique Visitors',
                data: <?php echo json_encode($chartData['visitors']); ?>,
                borderColor: '#10b981', // Emerald 500
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#10b981',
                pointRadius: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.3)' }, border: { dash: [4, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Channels Doughnut Chart
    const ctxChannels = document.getElementById('channelsDoughnutChart').getContext('2d');
    const channelsLabels = <?php echo json_encode(array_keys($channelsData)); ?>;
    const channelsData = <?php echo json_encode(array_values($channelsData)); ?>;
    
    // Assign colors based on SiteKit aesthetic
    const bgColors = [];
    channelsLabels.forEach(label => {
        if (label === 'Direct') bgColors.push('#FBBF24');
        else if (label === 'Organic Search') bgColors.push('#8B5CF6');
        else if (label === 'Referral') bgColors.push('#60A5FA');
        else bgColors.push('#F472B6'); // Social
    });

    new Chart(ctxChannels, {
        type: 'doughnut',
        data: {
            labels: channelsLabels.length ? channelsLabels : ['Direct'],
            datasets: [{
                data: channelsData.length ? channelsData : [100],
                backgroundColor: bgColors.length ? bgColors : ['#FBBF24'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false }, tooltip: { enabled: true } }
        }
    });

    // 3. Search Impressions Line Chart
    const ctxSearch = document.getElementById('searchLineChart').getContext('2d');
    new Chart(ctxSearch, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartData['labels']); ?>,
            datasets: [{
                label: 'Impressions',
                data: <?php echo json_encode($chartData['views']); ?>, // Mocking impressions via views
                borderColor: '#3b82f6', // Blue 500
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.4,
            }, {
                label: 'Previous period',
                data: <?php echo json_encode(array_reverse($chartData['views'])); ?>, // Mock previous period
                borderColor: '#3b82f6',
                borderWidth: 1.5,
                borderDash: [5, 5],
                pointRadius: 0,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', align: 'start', labels: { boxWidth: 20, boxHeight: 2, usePointStyle: false } } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>


<!-- ── AI TRENDING TOPICS ──────────────────────────────────── -->
<div class="mt-8 mb-8 glass-card rounded-xl overflow-hidden" id="trends-section">
    <div class="p-6 border-b border-navy-700/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500/20 to-rose-500/20 text-orange-400 shrink-0">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-text-primary tracking-wide">AI Trending Topics</h2>
                    <p class="text-sm text-text-muted mt-0.5">Get AI-generated article ideas with headlines, content, SEO optimization — publish in one click.</p>
                </div>
            </div>
            <button id="generateTrendsBtn" onclick="generateTrends()" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-rose-500 rounded-xl hover:from-orange-600 hover:to-rose-600 transition-all shadow-lg shadow-orange-500/20 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-navy-900">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Generate Trending Topics
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="trendsLoading" class="hidden p-12 text-center">
        <div class="inline-flex items-center gap-3 bg-navy-950/60 px-6 py-4 rounded-xl border border-navy-700/30">
            <svg class="animate-spin h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <span class="text-text-muted text-sm font-medium">AI is analyzing trends and generating article ideas...</span>
        </div>
    </div>

    <!-- Error State -->
    <div id="trendsError" class="hidden p-6">
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p id="trendsErrorMsg"></p>
        </div>
    </div>

    <!-- Results Container -->
    <div id="trendsResults" class="hidden">
        <div class="p-6 border-b border-navy-700/30 flex items-center justify-between">
            <span class="text-sm text-text-muted font-medium"><span id="trendsCount">0</span> topic suggestions generated</span>
            <button onclick="generateTrends()" class="text-xs text-accent-blue hover:text-accent-blue-hover font-bold transition-colors focus:outline-none">Regenerate</button>
        </div>
        <div id="trendsCards" class="divide-y divide-navy-700/20"></div>
    </div>
</div>

<script>
function generateTrends() {
    const btn = document.getElementById('generateTrendsBtn');
    const loading = document.getElementById('trendsLoading');
    const errorBox = document.getElementById('trendsError');
    const results = document.getElementById('trendsResults');
    
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    loading.classList.remove('hidden');
    errorBox.classList.add('hidden');
    results.classList.add('hidden');
    
    fetch("<?php echo e(route('admin.trends.generate')); ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        loading.classList.add('hidden');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        
        if (data.error) {
            document.getElementById('trendsErrorMsg').textContent = data.error;
            errorBox.classList.remove('hidden');
            return;
        }
        
        const topics = data.topics || [];
        document.getElementById('trendsCount').textContent = topics.length;
        renderTopics(topics);
        results.classList.remove('hidden');
    })
    .catch(err => {
        loading.classList.add('hidden');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        document.getElementById('trendsErrorMsg').textContent = 'Network error: ' + err.message;
        errorBox.classList.remove('hidden');
    });
}

function renderTopics(topics) {
    const container = document.getElementById('trendsCards');
    container.innerHTML = '';
    
    topics.forEach((topic, i) => {
        const card = document.createElement('div');
        card.className = 'p-6 hover:bg-navy-950/20 transition-colors';
        card.id = `trend-card-${i}`;
        
        const tagsHtml = (topic.tags || []).map(t => 
            `<span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-navy-900 text-text-muted border border-navy-700/30">#${t}</span>`
        ).join('');

        // Score helper
        function scoreBadge(label, icon, value, colorClass, isRisk = false) {
            const displayVal = value ?? '--';
            const barWidth = Math.min(100, Math.max(0, value || 0));
            const riskLabel = isRisk ? (value <= 15 ? 'Low' : value <= 40 ? 'Medium' : 'High') : '';
            const qualityLabel = !isRisk ? (value >= 80 ? 'Excellent' : value >= 60 ? 'Good' : value >= 40 ? 'Fair' : 'Low') : '';
            const sublabel = isRisk ? riskLabel : qualityLabel;
            return `
                <div class="bg-navy-950/50 rounded-xl p-3 border border-navy-700/20">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted flex items-center gap-1">${icon} ${label}</span>
                        <span class="text-sm font-bold ${colorClass}">${displayVal}<span class="text-[10px] text-text-muted font-medium">/100</span></span>
                    </div>
                    <div class="w-full bg-navy-900 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full ${colorClass.replace('text-', 'bg-')} transition-all" style="width: ${barWidth}%"></div>
                    </div>
                    <span class="text-[9px] text-text-muted mt-1 block">${sublabel}</span>
                </div>`;
        }

        const scoresHtml = `
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4 pl-11">
                ${scoreBadge('SEO Score', '🔍', topic.seo_score, 'text-emerald-400')}
                ${scoreBadge('GEO Score', '🌍', topic.geo_score, 'text-blue-400')}
                ${scoreBadge('Authenticity', '✅', topic.authenticity_score, 'text-violet-400')}
                ${scoreBadge('Plagiarism Risk', '📋', topic.plagiarism_risk, topic.plagiarism_risk <= 15 ? 'text-emerald-400' : topic.plagiarism_risk <= 40 ? 'text-amber-400' : 'text-rose-400', true)}
            </div>`;
        
        card.innerHTML = `
            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                <div class="flex-1 min-w-0">
                    <!-- Header -->
                    <div class="flex items-start gap-3 mb-3">
                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-rose-500 text-white font-bold text-sm flex items-center justify-center">${i + 1}</span>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-text-primary leading-snug">${topic.headline}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-accent-blue/10 text-accent-blue border border-accent-blue/20">${topic.category}</span>
                                ${tagsHtml}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Excerpt -->
                    <p class="text-text-secondary text-sm leading-relaxed mb-1 pl-11">${topic.excerpt}</p>

                    <!-- Score Badges -->
                    ${scoresHtml}
                    
                    <!-- Expandable Content -->
                    <div class="pl-11 mt-4">
                        <button onclick="toggleContent(${i})" class="text-xs font-bold text-accent-blue hover:text-accent-blue-hover transition-colors flex items-center gap-1 mb-3 focus:outline-none">
                            <svg id="expand-icon-${i}" class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            <span id="expand-text-${i}">Show full article draft</span>
                        </button>
                        <div id="content-${i}" class="hidden">
                            <div class="prose prose-sm prose-invert max-w-none bg-navy-950/40 rounded-xl p-5 border border-navy-700/20 mb-4 text-text-secondary leading-relaxed">
                                ${topic.content}
                            </div>
                            
                            <!-- SEO Preview -->
                            <div class="bg-navy-950/40 rounded-xl p-4 border border-navy-700/20 mb-4">
                                <h4 class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    Google Search Preview
                                </h4>
                                <p class="text-accent-blue text-base font-medium leading-snug hover:underline cursor-default">${topic.meta_title}</p>
                                <p class="text-emerald-500 text-xs mt-1 font-mono">atomni.com › article › ${(topic.headline || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').substring(0, 40)}</p>
                                <p class="text-text-muted text-sm mt-1 line-clamp-2">${topic.meta_description}</p>
                            </div>
                            
                            <!-- Image Prompt -->
                            <div class="bg-navy-950/40 rounded-xl p-4 border border-navy-700/20">
                                <h4 class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Suggested Featured Image
                                </h4>
                                <p class="text-text-secondary text-sm italic">"${topic.image_prompt}"</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Publish Button -->
                <div class="flex-shrink-0 flex flex-col gap-2 lg:pt-2">
                    <button onclick="publishTopic(${i})" id="publish-btn-${i}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-navy-900 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        Publish Now
                    </button>
                    <div id="publish-result-${i}"></div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
    
    // Store topics globally for publish
    window._trendTopics = topics;
}

function toggleContent(i) {
    const content = document.getElementById(`content-${i}`);
    const icon = document.getElementById(`expand-icon-${i}`);
    const text = document.getElementById(`expand-text-${i}`);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
        text.textContent = 'Hide article draft';
    } else {
        content.classList.add('hidden');
        icon.style.transform = '';
        text.textContent = 'Show full article draft';
    }
}

function publishTopic(i) {
    const topic = window._trendTopics[i];
    if (!topic) return;
    
    window._publishingIndex = i;
    
    // Show the modal
    const modal = document.getElementById('publishModal');
    const promptText = document.getElementById('modalImagePrompt');
    promptText.textContent = topic.image_prompt || 'No image prompt available';
    modal.classList.remove('hidden');
    modal.querySelector('.modal-panel').classList.add('animate-modal-in');
}

function closePublishModal() {
    const modal = document.getElementById('publishModal');
    modal.classList.add('hidden');
    window._publishingIndex = null;
    
    // Reset upload UI
    const zone = document.getElementById('manualUploadZone');
    if(zone) zone.classList.add('hidden');
    const fileInput = document.getElementById('manualImageUpload');
    if(fileInput) fileInput.value = '';
    const placeholder = document.getElementById('uploadPlaceholder');
    if(placeholder) placeholder.classList.remove('hidden');
    const preview = document.getElementById('uploadPreview');
    if(preview) preview.classList.add('hidden');
    const list = document.getElementById('selectedFileList');
    if(list) list.innerHTML = '';
}

function toggleManualUpload() {
    const zone = document.getElementById('manualUploadZone');
    zone.classList.toggle('hidden');
}

function handleFileSelect(input) {
    const placeholder = document.getElementById('uploadPlaceholder');
    const preview = document.getElementById('uploadPreview');
    const list = document.getElementById('selectedFileList');
    
    list.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
        
        Array.from(input.files).forEach(file => {
            const li = document.createElement('li');
            li.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            list.appendChild(li);
        });
    } else {
        placeholder.classList.remove('hidden');
        preview.classList.add('hidden');
    }
}

function confirmPublish(imageMode) {
    const i = window._publishingIndex;
    const topic = window._trendTopics[i];
    if (!topic) return;
    
    const btn = document.getElementById(`publish-btn-${i}`);
    const originalBtn = btn.innerHTML;
    
    // If manual mode, validate file existence before closing modal
    if (imageMode === 'manual') {
        const fileInput = document.getElementById('manualImageUpload');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please select at least one file to upload.');
            return;
        }
    }
    
    // Close modal
    closePublishModal();
    
    const resultDiv = document.getElementById(`publish-result-${i}`);
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    
    const statusText = imageMode === 'ai' ? 'Generating image & publishing...' : (imageMode === 'manual' ? 'Uploading & publishing...' : 'Publishing...');
    btn.innerHTML = `<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> ${statusText}`;
    
    const payload = { ...topic, image_mode: imageMode };
    
    // Convert JSON payload to FormData to support file uploads
    const formData = new FormData();
    for (const key in payload) {
        if (Array.isArray(payload[key])) {
            payload[key].forEach(val => formData.append(key + '[]', val));
        } else {
            formData.append(key, payload[key]);
        }
    }
    
    if (imageMode === 'manual') {
        const fileInput = document.getElementById('manualImageUpload');
        for (let j = 0; j < fileInput.files.length; j++) {
            formData.append('manual_uploads[]', fileInput.files[j]);
        }
    }
    
    fetch("<?php echo e(route('admin.trends.publish')); ?>", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
            // Do not set Content-Type; standard browser behavior handles multipart boundary form-data
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Published!`;
            btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'opacity-50');
            btn.classList.add('bg-emerald-800', 'text-emerald-300');
            
            let resultHtml = `
                <div class="flex flex-col gap-2 mt-2">
                    <div class="flex gap-2">
                        <a href="${data.post_url}" target="_blank" class="text-xs font-bold text-accent-blue hover:text-accent-blue-hover transition-colors">View on site →</a>
                        <a href="${data.admin_url}" target="_blank" class="text-xs font-bold text-text-muted hover:text-text-primary transition-colors">Edit in admin →</a>
                    </div>`;
            
            if (data.featured_image_url) {
                const badgeText = data.image_mode === 'manual' ? '✓ Uploaded image saved' : '✓ AI image generated & saved';
                resultHtml += `
                    <div class="mt-1">
                        <img loading="lazy" src="${data.featured_image_url}" alt="Featured" class="w-36 h-20 object-cover rounded-lg border border-navy-700/30 shadow-sm">
                        <span class="text-[9px] text-emerald-400 font-bold block mt-1">${badgeText}</span>
                    </div>`;
            } else if (data.image_mode === 'ai') {
                resultHtml += `<span class="text-[9px] text-amber-400 font-bold mt-1">⚠ Image generation failed — published without image</span>`;
            }
            
            resultHtml += '</div>';
            resultDiv.innerHTML = resultHtml;
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = originalBtn;
            alert(data.error || 'Failed to publish. Please try again.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.innerHTML = originalBtn;
        alert('Network error: ' + err.message);
    });
}
</script>

<!-- Publish Image Choice Modal -->
<div id="publishModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
    <div class="modal-panel w-full max-w-lg mx-4 bg-navy-900 border border-navy-700/40 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-navy-700/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-text-primary">Featured Image</h3>
                        <p class="text-xs text-text-muted">Choose how to set the article's featured image</p>
                    </div>
                </div>
                <button onclick="closePublishModal()" class="text-text-muted hover:text-text-primary transition-colors p-1 rounded-lg hover:bg-navy-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- AI Image Prompt Preview -->
            <div class="bg-navy-950/50 rounded-xl p-4 border border-navy-700/20">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-2">🖼️ AI Image Prompt</p>
                <p id="modalImagePrompt" class="text-sm text-text-secondary italic leading-relaxed"></p>
            </div>

            <!-- Option Buttons -->
            <div class="space-y-3">
                <!-- AI Generate -->
                <button onclick="confirmPublish('ai')" class="w-full flex items-center gap-4 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 hover:bg-emerald-500/10 transition-all group text-left">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-400">Generate AI Image</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Create a unique image using AI based on the prompt above. Includes Atomni watermark.</p>
                    </div>
                </button>

                <!-- Manual Upload Toggle -->
                <button onclick="toggleManualUpload()" class="w-full flex items-center gap-4 p-4 rounded-xl border border-accent-blue/20 bg-accent-blue/5 hover:bg-accent-blue/10 transition-all group text-left">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-accent-blue/20 to-accent-blue-hover/20 text-accent-blue flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-accent-blue">Upload Custom Media</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Upload your own images from your computer.</p>
                    </div>
                </button>
                
                <!-- Manual Upload Dropzone -> hidden by default -->
                <div id="manualUploadZone" class="hidden mt-2 border-2 border-dashed border-navy-700/50 rounded-xl p-6 text-center hover:border-accent-blue/50 transition-colors bg-navy-950/30 relative overflow-hidden">
                    <input type="file" id="manualImageUpload" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp,image/avif" onchange="handleFileSelect(this)">
                    <div id="uploadPlaceholder" class="pointer-events-none">
                        <svg class="w-8 h-8 mx-auto text-text-muted mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        <p class="text-sm font-bold text-text-primary">Drag & drop files or click to browse</p>
                        <p class="text-[11px] text-text-muted mt-1 leading-relaxed">Supports multiple files: JPG, PNG, GIF, WEBP, AVIF, SVG<br>(Max 10MB each)</p>
                    </div>
                    <div id="uploadPreview" class="hidden text-left relative z-20">
                        <p class="text-sm font-bold text-emerald-400 mb-2">Selected files:</p>
                        <ul id="selectedFileList" class="text-xs text-text-primary font-medium space-y-1 list-disc list-inside bg-navy-900/50 rounded-lg p-2 border border-navy-700/30"></ul>
                        <button onclick="confirmPublish('manual')" class="mt-4 w-full px-4 py-2 bg-accent-blue text-white rounded-lg font-bold hover:bg-accent-blue-hover transition-colors text-sm shadow-sm">Upload & Publish 🚀</button>
                    </div>
                </div>

                <!-- Skip -->
                <button onclick="confirmPublish('skip')" class="w-full flex items-center gap-4 p-4 rounded-xl border border-navy-700/20 bg-navy-950/30 hover:bg-navy-950/50 transition-all group text-left">
                    <div class="w-12 h-12 rounded-xl bg-navy-800 text-text-muted flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-text-primary">Skip for Now</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Publish without a featured image. You can add one later from the post editor.</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-navy-950/30 border-t border-navy-700/20">
            <button onclick="closePublishModal()" class="text-xs font-bold text-text-muted hover:text-text-primary transition-colors">Cancel</button>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trafficChart').getContext('2d');
        const gradientViews = ctx.createLinearGradient(0, 0, 0, 400);
        gradientViews.addColorStop(0, 'rgba(45, 127, 249, 0.4)');
        gradientViews.addColorStop(1, 'rgba(45, 127, 249, 0)');
        
        const gradientVisitors = ctx.createLinearGradient(0, 0, 0, 400);
        gradientVisitors.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradientVisitors.addColorStop(1, 'rgba(16, 185, 129, 0)');

        const dailyData = {
            labels: <?php echo json_encode($chartData['labels']); ?>,
            views: <?php echo json_encode($chartData['views']); ?>,
            visitors: <?php echo json_encode($chartData['visitors']); ?>

        };

        const hourlyData = {
            labels: <?php echo isset($chartHourlyData) ? json_encode($chartHourlyData['labels']) : '[]'; ?>,
            views: <?php echo isset($chartHourlyData) ? json_encode($chartHourlyData['views']) : '[]'; ?>,
            visitors: <?php echo isset($chartHourlyData) ? json_encode($chartHourlyData['visitors']) : '[]'; ?>

        };

        let trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyData.labels,
                datasets: [
                    {
                        label: 'Page Views',
                        data: dailyData.views,
                        borderColor: '#2D7FF9', // electric blue
                        backgroundColor: gradientViews,
                        borderWidth: 3,
                        pointBackgroundColor: '#2D7FF9',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Unique Visitors',
                        data: dailyData.visitors,
                        borderColor: '#10B981', // emerald
                        backgroundColor: gradientVisitors,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: document.documentElement.classList.contains('light') ? '#64748b' : '#94a3b8',
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: document.documentElement.classList.contains('light') ? 'rgba(255, 255, 255, 0.95)' : 'rgba(15, 23, 42, 0.9)',
                        titleColor: document.documentElement.classList.contains('light') ? '#0f172a' : '#fff',
                        bodyColor: document.documentElement.classList.contains('light') ? '#334155' : '#e2e8f0',
                        borderColor: document.documentElement.classList.contains('light') ? 'rgba(203, 213, 225, 0.5)' : 'rgba(51, 65, 85, 0.5)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        usePointStyle: true,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: document.documentElement.classList.contains('light') ? 'rgba(203, 213, 225, 0.3)' : 'rgba(51, 65, 85, 0.3)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('light') ? '#64748b' : '#94a3b8',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        grid: {
                            color: document.documentElement.classList.contains('light') ? 'rgba(203, 213, 225, 0.3)' : 'rgba(51, 65, 85, 0.3)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: document.documentElement.classList.contains('light') ? '#64748b' : '#94a3b8',
                            padding: 10
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Make toggle function available globally
        window.toggleTrafficChart = function(type) {
            const btnDaily = document.getElementById('btnDaily');
            const btnHourly = document.getElementById('btnHourly');
            
            if (type === 'daily') {
                btnDaily.className = "px-3 py-1.5 text-xs font-bold rounded-md bg-accent-blue text-white shadow-md transition-colors focus:outline-none";
                btnHourly.className = "px-3 py-1.5 text-xs font-bold rounded-md text-text-muted hover:text-text-primary transition-colors focus:outline-none";
                
                trafficChart.data.labels = dailyData.labels;
                trafficChart.data.datasets[0].data = dailyData.views;
                trafficChart.data.datasets[1].data = dailyData.visitors;
            } else {
                btnHourly.className = "px-3 py-1.5 text-xs font-bold rounded-md bg-accent-blue text-white shadow-md transition-colors focus:outline-none";
                btnDaily.className = "px-3 py-1.5 text-xs font-bold rounded-md text-text-muted hover:text-text-primary transition-colors focus:outline-none";
                
                trafficChart.data.labels = hourlyData.labels;
                trafficChart.data.datasets[0].data = hourlyData.views;
                trafficChart.data.datasets[1].data = hourlyData.visitors;
            }
            trafficChart.update();
        };
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>