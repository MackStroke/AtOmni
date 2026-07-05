<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPublicFolder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Bypass redirection entirely on local environments (localhost, 127.0.0.1) to avoid 404s/routing conflicts
        if (in_array($host, ['localhost', '127.0.0.1'])) {
            return $next($request);
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // Safely capture and strip '/public/' from the URI while fully preserving any parent subdirectory installation
        if (preg_match('~^(.*)/public/(.*)$~i', $uri, $matches)) {
            $cleanPath = $matches[1] . '/' . $matches[2];

            // Avoid redirecting static assets (css, js, images, storage, etc.)
            if (!preg_match('~\.(css|js|png|jpg|jpeg|gif|svg|webp|avif|ico|woff|woff2|ttf|xml|txt|json|map)$~i', $uri) &&
                !str_contains($cleanPath, '/storage/') &&
                !str_contains($cleanPath, '/build/') &&
                !str_contains($cleanPath, '/assets/')) {

                // Build full clean URL (works for both atomni.in and www.atomni.in)
                $cleanUrl = $request->getSchemeAndHttpHost() . $cleanPath;
                return redirect()->to($cleanUrl, 301);
            }
        }

        return $next($request);
    }
}
