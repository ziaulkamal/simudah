<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAppAccess
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-App-Signature');
        $timestamp = $request->header('X-App-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json(['error' => 'Missing signature or timestamp'], 403);
        }

        // Validasi timestamp
        if (abs(time() - (int)$timestamp) > 300) {
            return response()->json(['error' => 'Expired timestamp'], 403);
        }

        $secret = env('APP_KEY');

        // 🔥 PAKAI RAW BODY
        $json = $request->getContent();

        $computed = hash_hmac('sha256', $json . $timestamp, $secret);

        // DEBUG (sementara)
        \Log::info('VERIFY DEBUG', [
            'raw_body' => $json,
            'timestamp' => $timestamp,
            'computed' => $computed,
            'received' => $signature,
        ]);

        if (!hash_equals($computed, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        return $next($request);
    }
}