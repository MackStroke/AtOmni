<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — gate specific route groups to one or more allowed roles.
 *
 * Usage in routes:
 *   ->middleware('role:super_admin')
 *   ->middleware('role:super_admin,editor')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles)) {
            // AJAX / JSON requests: 403 JSON
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden — insufficient privileges.'], 403);
            }

            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
