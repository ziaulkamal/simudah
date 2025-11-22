<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifyApiSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signatureHeader = $request->header('X-Signature');
        $timestampHeader = $request->header('X-Timestamp');
        $tokenHeader     = $request->header('X-Token');

        if (!$signatureHeader || !$timestampHeader || !$tokenHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Missing signature headers.'
            ], 403);
        }

        // Batasi timestamp ±5 menit
        if (abs(time() - (int)$timestampHeader) > 300) {
            return response()->json([
                'success' => false,
                'message' => 'Expired timestamp.'
            ], 403);
        }

        // Ambil signature_session dari session
        $signatureSession = Session::get('signature_session');
        if (!$signatureSession || $signatureSession['token'] !== $tokenHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.'
            ], 403);
        }

        // Validasi HMAC
        $body = $request->all();
        ksort($body);
        $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $secret = env('APP_KEY'); // Bisa disesuaikan secret khusus API
        $computed = hash_hmac('sha256', $json . $timestampHeader, $secret);

        if (!hash_equals($computed, $signatureHeader)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.'
            ], 403);
        }

        return $next($request);
    }
}
