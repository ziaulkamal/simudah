<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        $loginName = Session::get('login_name');
        $roleName = Session::get('role_name');
        $roleLevel = Session::get('role_level');
        $signatureSession = Session::get('signature_session');
        $expiresAt = Session::get('expires_at');

        // Cek semua variable session ada dan belum expired
        if (!$loginName || !$roleName || !$roleLevel || !$signatureSession || !$expiresAt || $expiresAt->isPast()) {
            session()->flash('login_modal', [
                'title' => 'Anda harus login',
                'message' => 'Silakan login untuk mengakses halaman ini.'
            ]);

            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}
