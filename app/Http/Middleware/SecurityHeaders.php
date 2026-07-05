<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');

            if (app()->environment('production')) {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }

            $csp = "default-src 'self' data: https: 'unsafe-inline'; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; font-src 'self' data: https:; img-src 'self' data: https: blob:; object-src 'none'; frame-ancestors 'self';";

            if (app()->environment('local')) {
                $csp = "default-src 'self' data: https: 'unsafe-inline' http://localhost:5173; script-src 'self' 'unsafe-inline' https: http://localhost:5173; style-src 'self' 'unsafe-inline' https: http://localhost:5173; font-src 'self' data: https:; img-src 'self' data: https: blob:; connect-src 'self' https: ws://localhost:5173 http://localhost:5173; object-src 'none'; frame-ancestors 'self';";
            }

            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
