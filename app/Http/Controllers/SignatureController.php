<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignatureController extends Controller
{
    function signatures(Request $request) {
        $body = $request->query(); // ambil data body dari query atau default
        $timestamp = time();
        ksort($body);
        $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $secret = env('APP_KEY');
        $signature = hash_hmac('sha256', $json . $timestamp, $secret);

        return response()->json([
            'signature' => $signature,
            'timestamp' => $timestamp,
        ]);
    }
}
