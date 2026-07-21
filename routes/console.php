<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Sitemap: flush every 24 hours as a safety-net ──────────────────────────
// Real-time invalidation is handled by SitemapObserver on model save/delete.
Schedule::call(function () {
    Cache::forget('sitemap_xml');
})->daily()->name('sitemap-cache-flush')->withoutOverlapping();

// ── Post Scheduler: auto-publish posts whose publish time has arrived ────────
Schedule::command('posts:publish-scheduled')
    ->everyMinute()
    ->name('publish-scheduled-posts')
    ->withoutOverlapping();



// ── GA4 Traffic Sync: pull yesterday's data from Google Analytics 4 ─────────
// Only runs if ga4_property_id + ga4_service_account_json are configured.
Schedule::command('ga4:sync --days=2')
    ->dailyAt('03:00')
    ->name('ga4-sync')
    ->withoutOverlapping()
    ->runInBackground();


Schedule::command('fetch:sports-fixtures')->hourly();

// ── AI News Agent: research, write, and publish breaking news articles daily ──
Schedule::command('agent:run-news')
    ->dailyAt('04:00')
    ->name('ai-news-agent-daily')
    ->withoutOverlapping()
    ->runInBackground();
