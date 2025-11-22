<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MultiGuardAuth
{
    public function handle($request, Closure $next, ...$guards)
    {
        // Jika guard tidak ditentukan di route, pakai default: web + api
        $guards = empty($guards) ? ['web', 'api'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Set guard aktif untuk request berjalan
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        // Jika request expecting JSON (API)
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Default untuk web
        return redirect()->guest(route('auth.login'));
    }
}
