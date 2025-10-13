<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppAccess
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-App-Signature');
        $timestamp = $request->header('X-App-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json(['error' => 'Missing signature or timestamp'], 403);
        }

        // batasi validitas timestamp 5 menit
        if (abs(time() - (int)$timestamp) > 300) {
            return response()->json(['error' => 'Expired timestamp'], 403);
        }

        $secret = env('APP_KEY');
        $body = $request->all();
        ksort($body);
        $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $computed = hash_hmac('sha256', $json . $timestamp, $secret);

        if (!hash_equals($computed, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        return $next($request);
    }
}
