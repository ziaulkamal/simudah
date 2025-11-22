<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AjaxSameOrigin
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Harus AJAX atau request JSON
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only AJAX requests allowed.'
            ], 403);
        }

        // 2. Ambil origin & referer
        $origin  = $request->headers->get('origin');
        $referer = $request->headers->get('referer');

        $appUrl = config('app.url'); // Contoh: https://example.com

        // 3. Wajib ada origin atau referer → mencegah Postman & curl
        if (!$origin && !$referer) {
            return response()->json([
                'success' => false,
                'message' => 'Origin or referer missing. Browser only.'
            ], 403);
        }

        // 4. Cek origin
        if ($origin && stripos($origin, $appUrl) !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid origin.'
            ], 403);
        }

        // 5. Cek referer
        if ($referer && stripos($referer, $appUrl) !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referer.'
            ], 403);
        }

        return $next($request);
    }
}
