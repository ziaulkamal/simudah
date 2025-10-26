<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CheckLoginSession
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $sessionId = Session::getId();
        $hasLogin = Session::has('login_id');
        $expiresAt = Session::get('expires_at');

        // Log::debug('Middleware CheckLoginSession', [
        //     'session_id' => $sessionId,
        //     'has_login' => $hasLogin,
        //     'expires_at' => $expiresAt,
        //     'cookie_domain' => config('session.domain'),
        //     'cookie_name' => config('session.cookie'),
        //     'all_session' => Session::all(),
        // ]);

        // Jangan flush langsung — pastikan dulu ada expiry & login_id
        if (!$hasLogin) {
            Log::warning('❌ Session invalid (no login_id)', [
                'session_id' => $sessionId,
                'cookies' => $request->cookies->all(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session not found',
                ], 401);
            }

            return redirect()->route('auth.login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        if ($expiresAt && now()->greaterThan($expiresAt)) {
            Log::warning('⏰ Session expired', [
                'session_id' => $sessionId,
                'expires_at' => $expiresAt,
            ]);
            Session::flush();
            return redirect()->route('auth.login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
        }

        if (Session::get('remember_me')) {
            Session::put('expires_at', now()->addDays(7));
        }

        return $next($request);
    }
}
