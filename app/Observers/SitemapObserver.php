<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

/**
 * SitemapObserver
 *
 * Attach this to any model whose changes should invalidate the sitemap cache.
 * Works for: Post, Category, Tag, Page, JobPosting
 */
class SitemapObserver
{
    /**
     * Bust the sitemap cache whenever a model is saved (created or updated).
     */
    public function saved($model): void
    {
        Cache::forget('sitemap_xml');
    }

    /**
     * Bust the sitemap cache whenever a model is deleted (including soft-deletes).
     */
    public function deleted($model): void
    {
        Cache::forget('sitemap_xml');
    }

    /**
     * Also bust on restore (soft-delete models).
     */
    public function restored($model): void
    {
        Cache::forget('sitemap_xml');
    }
}
