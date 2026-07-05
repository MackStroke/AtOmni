<?php

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
