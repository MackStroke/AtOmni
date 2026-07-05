<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\JobPosting;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Observers\SitemapObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Password::defaults(function () {
            $rule = Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });

        Paginator::defaultView('vendor.pagination.atomni');
        Paginator::defaultSimpleView('vendor.pagination.atomni');

        // ── Auto-invalidate sitemap cache on content changes ───
        Post::observe(SitemapObserver::class);
        Category::observe(SitemapObserver::class);
        Tag::observe(SitemapObserver::class);
        Page::observe(SitemapObserver::class);
        JobPosting::observe(SitemapObserver::class);

        // ── Share dynamic menus with frontend partials ─────────
        View::composer(['partials.header', 'partials.footer'], function ($view) {
            // Header navbar links
            $headerMenu = Menu::getByLocation('header_navbar');

            // Mega menu (with nested children)
            $megaMenu = Menu::getByLocation('mega_menu');

            // Footer columns
            $footerCompany    = Menu::getByLocation('footer_company');
            $footerCategories = Menu::getByLocation('footer_categories');
            $footerLegal      = Menu::getByLocation('footer_legal');
            $footerResources  = Menu::getByLocation('footer_resources');

            $view->with(compact(
                'headerMenu',
                'megaMenu',
                'footerCompany',
                'footerCategories',
                'footerLegal',
                'footerResources'
            ));
        });
    }
}
