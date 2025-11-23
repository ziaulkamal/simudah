<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah session login valid
        $loginName = Session::get('login_name');
        $roleName = Session::get('role_name');
        $roleLevel = Session::get('role_level');
        $signatureSession = Session::get('signature_session');
        $expiresAt = Session::get('expires_at');

        $isLoggedIn = $loginName && $roleName && $roleLevel && $signatureSession && $expiresAt && !$expiresAt->isPast();

        if ($isLoggedIn) {
            // Jika sudah login, redirect ke halaman utama
            return redirect()->to('/');
        }

        return $next($request);
    }
}
