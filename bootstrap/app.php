<?php

if (!class_exists('ZipArchive')) {
    class ZipArchive {
        const CM_DEFAULT = 8;
        const CM_STORE = 0;
        const CM_SHRINK = 1;
        const CM_REDUCE_1 = 2;
        const CM_REDUCE_2 = 3;
        const CM_REDUCE_3 = 4;
        const CM_REDUCE_4 = 5;
        const CM_IMPLODE = 6;
        const CM_DEFLATE = 8;
        const CM_DEFLATE64 = 9;
        const CM_PKWARE_IMPLODE = 10;
        const CM_BZIP2 = 12;
        const CM_LZMA = 14;
        const CM_TERSE = 18;
        const CM_LZ77 = 19;
        const CM_WAVPACK = 97;
        const CM_PPMD = 98;
        
        const CREATE = 1;
        const EXCL = 2;
        const CHECKCONS = 4;
        const OVERWRITE = 8;
        const RDONLY = 16;
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Append to the web group so every frontend page view is tracked
        $middleware->web(append: [
            \App\Http\Middleware\RedirectPublicFolder::class,
            \App\Http\Middleware\RecordPageView::class,
            \App\Http\Middleware\EnforceCanonicalDomain::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Remove the default X-Frame-Options middleware in favor of CSP frame-ancestors
        $middleware->remove(\Illuminate\Http\Middleware\FrameGuard::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
