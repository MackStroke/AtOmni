<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnforceCanonicalDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = config('app.url');

        if (!$appUrl || empty($appUrl) || $appUrl === 'http://localhost') {
            return $next($request);
        }

        $parsedUrl = parse_url($appUrl);
        $canonicalHost = $parsedUrl['host'] ?? null;
        $canonicalScheme = $parsedUrl['scheme'] ?? 'https';

        if (!$canonicalHost) {
            return $next($request);
        }

        $currentHost = $request->getHost();
        $currentScheme = $request->getScheme();

        // If the current host or scheme doesn't match the canonical one, redirect.
        if ($currentHost !== $canonicalHost || $currentScheme !== $canonicalScheme) {
            $redirectUrl = $canonicalScheme . '://' . $canonicalHost . $request->getRequestUri();
            return redirect()->away($redirectUrl, 301);
        }

        return $next($request);
    }
}
