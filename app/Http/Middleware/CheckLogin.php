<?php

namespace App\Http\Middleware;

use App\Models\People;
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

        if ($request->routeIs('logout')) {
            return $next($request);
        }
        // ==== CEK LOGIN ====
        if (!$loginName || !$roleName || !$roleLevel || !$signatureSession || !$expiresAt || $expiresAt->isPast()) {
            session()->flash('login_modal', [
                'title' => 'Anda harus login',
                'message' => 'Silakan login untuk mengakses halaman ini.'
            ]);

            return redirect()->route('auth.login');
        }

        // ==== AUTO REDIRECT KHUSUS ROLE LEVEL 3 ====
        if ($roleLevel == 3) {

            // Cek apakah user sudah berada di halaman detail,
            // jika iya jangan redirect (hindari infinite redirect)
            if (!$request->routeIs('customer.view')) {

                $peopleId = Session::get('people.id');

                if ($peopleId) {
                    $people = People::find($peopleId);

                    if ($people && $people->identity_hash) {
                        return redirect()->route('customer.view', [
                            'hash' => $people->identity_hash
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
