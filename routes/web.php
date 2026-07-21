<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\NewsletterSubscribeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ContactQueryController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ToolsController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\DonorController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Home ──────────────────────────────────────────────────────────
Route::get('/', [FrontendController::class, 'home'])->name('home');

// ── Content Pages ─────────────────────────────────────────────────
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category');
Route::get('/location/{slug}', [FrontendController::class, 'location'])->name('location');
Route::get('/article/{slug}', [FrontendController::class, 'article'])->name('frontend.article');
Route::post('/article/{slug}/comment', [FrontendController::class, 'storeComment'])->name('frontend.article.comment');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/explore', [FrontendController::class, 'explore'])->name('explore');
Route::get('/api/search/suggestions', [FrontendController::class, 'searchSuggestions'])->name('api.search.suggestions');
Route::post('/api/analytics/ping', [\App\Http\Controllers\Api\AnalyticsController::class, 'ping'])->name('api.analytics.ping');
// ── Newsletter Subscribe ──────────────────────────────────────────
Route::post('/subscribe', [NewsletterSubscribeController::class, 'subscribe'])->name('subscribe');

// ── Sitemap ───────────────────────────────────────────────────────
Route::get('/sitemap.xml', [FrontendController::class, 'sitemap'])
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\RecordPageView::class,
    ])
    ->name('sitemap');

// ── RSS Feed ──────────────────────────────────────────────────────
Route::get('/feed.xml', [RssFeedController::class, 'index'])
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\RecordPageView::class,
    ])
    ->name('feed');
Route::redirect('/feed', '/feed.xml', 301)->name('feed.redirect');

// ── AdSense Ads.txt ───────────────────────────────────────────────
Route::get('/ads.txt', function () {
    return response(\App\Models\Setting::get('adsense_ads_txt', ''), 200)
        ->header('Content-Type', 'text/plain');
});

// ── AI Automation & Marketing Pages ───────────────────────────────
Route::view('/use-cases/client-intake-automation', 'pages.use-cases.client-intake-automation')->name('use-cases.client-intake');
Route::view('/use-cases/document-processing-automation', 'pages.use-cases.document-processing-automation')->name('use-cases.document-processing');
Route::view('/compare/atomni-vs-zapier', 'pages.compare.atomni-vs-zapier')->name('compare.zapier');

// ── Company Pages ─────────────────────────────────────────────────
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');
Route::view('/advertise', 'pages.advertise')->name('advertise');
Route::get('/careers', [\App\Http\Controllers\CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/{job:slug}', [\App\Http\Controllers\CareerController::class, 'show'])->name('careers.show');
Route::post('/careers/{job:slug}/apply', [\App\Http\Controllers\CareerController::class, 'apply'])->name('careers.apply');
Route::view('/press-kit', 'pages.press-kit')->name('press-kit');
Route::get('/donate', [FrontendController::class, 'donate'])->name('donate');

// ── Legal Pages (DB-editable) ─────────────────────────────────────
Route::get('/privacy', [FrontendController::class, 'legalPage'])->defaults('slug', 'privacy')->name('privacy');
Route::get('/terms', [FrontendController::class, 'legalPage'])->defaults('slug', 'terms')->name('terms');
Route::get('/cookies', [FrontendController::class, 'legalPage'])->defaults('slug', 'cookies')->name('cookies');
Route::get('/dmca', [FrontendController::class, 'legalPage'])->defaults('slug', 'dmca')->name('dmca');
Route::get('/accessibility', [FrontendController::class, 'legalPage'])->defaults('slug', 'accessibility')->name('accessibility');
Route::get('/corrections', [FrontendController::class, 'legalPage'])->defaults('slug', 'corrections')->name('corrections');

// ── Authentication ──────────────────────────────────────────────────
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.submit')->middleware(['guest', 'throttle:5,1']);
Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
Route::match(['get', 'post'], '/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin Panel ───────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── All admin roles (super_admin, editor, author) ──────────────

    // TEMPORARY ROUTE: Run score generator on production
    Route::get('/run-scores-update', function() {
        if (auth()->user()->role !== 'super_admin') abort(403);
        \Illuminate\Support\Facades\Artisan::call('posts:analyze-scores', ['--all' => true]);
        return "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre><p>Done! You can now safely remove this route from routes/web.php</p>";
    });

    // Dashboard — every admin role sees it, but content is scoped in controller
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Generic Bulk Action Route
    Route::post('/bulk/{resource}', [\App\Http\Controllers\Admin\BulkActionController::class, 'handle'])->name('bulk.handle');

    // AI Trends (post-level feature — editor+author)
    Route::middleware('role:super_admin,editor,author')->group(function () {
        Route::post('/trends/generate', [\App\Http\Controllers\Admin\TrendsController::class, 'generate'])->name('trends.generate');
        Route::post('/trends/publish',  [\App\Http\Controllers\Admin\TrendsController::class, 'publish'])->name('trends.publish');
    });

    // Posts — all roles, author scoped to own in controller
    Route::middleware('role:super_admin,editor,author')->group(function () {
        Route::get('posts/export',            [PostController::class, 'export'])->name('posts.export');
        Route::get('posts/export-sample',     [PostController::class, 'exportSample'])->name('posts.export-sample');
        Route::post('posts/import',           [PostController::class, 'import'])->name('posts.import');
        Route::post('posts/check-plagiarism', [PostController::class, 'checkPlagiarism'])->name('posts.check-plagiarism');
        Route::post('posts/auto-taxonomy',    [PostController::class, 'autoTaxonomy'])->name('posts.auto-taxonomy');
        Route::post('posts/analyze-seo',      [PostController::class, 'analyzeSeo'])->name('posts.analyze-seo');
        Route::post('posts/{id}/analyze-content', [\App\Http\Controllers\Admin\ContentAnalysisController::class, 'analyze'])->name('posts.analyze-content');
        Route::post('posts/suggest-faqs',     [PostController::class, 'suggestFaqs'])->name('posts.suggest-faqs');
        Route::post('posts/{post}/kill',      [PostController::class, 'kill'])->name('posts.kill');
        Route::post('posts/analyze-all-scores', [PostController::class, 'analyzeAllScores'])->name('posts.analyze-all-scores')->middleware('role:super_admin,editor');
        Route::post('posts/analyze-all-taxonomy', [PostController::class, 'analyzeAllTaxonomy'])->name('posts.analyze-all-taxonomy')->middleware('role:super_admin,editor');
        Route::resource('posts', PostController::class)->except(['show']);
    });

    // Media — all roles, author scoped to own in controller
    Route::middleware('role:super_admin,editor,author')->group(function () {
        Route::resource('media', MediaController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::put('media/{medium}/crop',        [MediaController::class, 'crop'])->name('media.crop');
        Route::get('media/{medium}/usage',       [MediaController::class, 'usage'])->name('media.usage');
        Route::delete('media/{medium}/original', [MediaController::class, 'destroyOriginal'])->name('media.destroy_original');
        Route::post('media/{medium}/alt-text',   [\App\Http\Controllers\Admin\AiController::class, 'generateAltText'])->name('media.alt_text');
    });

    // ── super_admin + editor only ──────────────────────────────────

    // Reports
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    });

    // Pages
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::resource('pages', PageController::class)->except(['show']);
    });

    // Team Members (public profile management)
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::resource('team-members', \App\Http\Controllers\Admin\TeamMemberController::class)->except(['show']);
    });

    // Categories
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::post('categories/auto-hierarchy', [CategoryController::class, 'autoHierarchy'])->name('categories.auto-hierarchy');
        Route::post('categories/auto-fill', [CategoryController::class, 'autoFill'])->name('categories.auto-fill');
        Route::post('categories/{category}/auto-fill', [CategoryController::class, 'autoFillSingle'])->name('categories.auto-fill-single');
        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    // Homepage Sections
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::post('homepage-sections/update-order', [HomepageSectionController::class, 'updateOrder'])->name('homepage-sections.update-order');
        Route::resource('homepage-sections', HomepageSectionController::class)->except(['show']);
    });

    // Comments
    Route::middleware('role:super_admin,editor')->group(function () {
        Route::resource('comments', CommentController::class)->only(['index', 'destroy']);
        Route::post('comments/{comment}/toggle-approve', [CommentController::class, 'toggleApprove'])->name('comments.toggle-approve');
    });

    // ── super_admin only ───────────────────────────────────────────

    // Team Logins / User Management
    Route::middleware('role:super_admin')->group(function () {
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{id}/restore',          [UserManagementController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

    // Menus
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('menus', MenuController::class)->except(['show', 'create']);
    });

    // Contact Queries
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('contacts', ContactQueryController::class)->only(['index', 'show', 'destroy']);
    });

    // Donors
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('donors', DonorController::class)->except(['show']);
    });

    // Newsletter
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('newsletter', NewsletterController::class)->only(['index', 'destroy']);
    });

    // Careers
    Route::middleware('role:super_admin')->name('careers.')->prefix('careers')->group(function () {
        Route::resource('jobs', \App\Http\Controllers\Admin\JobPostingController::class);
        Route::get('applications/{application}/download-resume', [\App\Http\Controllers\Admin\JobApplicationController::class, 'downloadResume'])->name('applications.download-resume');
        Route::resource('applications', \App\Http\Controllers\Admin\JobApplicationController::class)->except(['create', 'store', 'edit']);
    });

    // Settings
    Route::middleware('role:super_admin')->name('settings.')->prefix('settings')->group(function () {
        Route::get('rss',  [SettingController::class, 'rss'])->name('rss');
        Route::put('rss',  [SettingController::class, 'updateRss']);

        Route::get('global', [SettingController::class, 'global'])->name('global');
        Route::put('global', [SettingController::class, 'updateGlobal']);

        Route::get('menus', [SettingController::class, 'menus'])->name('menus');
        Route::put('menus', [SettingController::class, 'updateMenus']);

        Route::get('integrations', [SettingController::class, 'integrations'])->name('integrations');
        Route::put('integrations', [SettingController::class, 'updateIntegrations'])->name('integrations.update');

        Route::get('ads', [SettingController::class, 'ads'])->name('ads');
        Route::put('ads', [SettingController::class, 'updateAds']);

        Route::get('social', [SettingController::class, 'social'])->name('social');
        Route::put('social', [SettingController::class, 'updateSocial']);

        Route::get('permalink', [SettingController::class, 'permalink'])->name('permalink');
        Route::put('permalink', [SettingController::class, 'updatePermalink']);
    });

    // Tools
    Route::middleware('role:super_admin')->name('tools.')->prefix('tools')->group(function () {
        Route::get('/',                          [ToolsController::class, 'index'])->name('index');
        Route::get('site-health',                [ToolsController::class, 'siteHealth'])->name('site-health');
        Route::get('import-export',              [ToolsController::class, 'importExport'])->name('import-export');
        Route::post('import-export/import',      [ToolsController::class, 'doImport'])->name('do-import');
        Route::get('import-export/export',       [ToolsController::class, 'doExport'])->name('do-export');
        Route::get('cache',                      [ToolsController::class, 'cacheManager'])->name('cache');
        Route::post('cache/clear',               [ToolsController::class, 'clearCache'])->name('cache.clear');
        Route::post('rss-import',                [ToolsController::class, 'runRssImport'])->name('rss-import');
        
        // AI News Agent
        Route::get('news-agent',                 [ToolsController::class, 'newsAgent'])->name('news-agent');
        Route::post('news-agent/run',            [ToolsController::class, 'runNewsAgent'])->name('news-agent.run');
        Route::post('news-agent/settings',       [ToolsController::class, 'saveNewsAgentSettings'])->name('news-agent.settings');
        Route::get('news-agent/logs',            [ToolsController::class, 'getNewsAgentLogs'])->name('news-agent.logs');
    });

    // Profile — always accessible by the logged-in user
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
        Route::put('/',         [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    });
});