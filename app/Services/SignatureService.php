<?php

namespace App\Services;

class SignatureService
{
    public function generate(array $body)
    {
        ksort($body);
        $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $timestamp = time();
        $secret = env('APP_KEY');
        $signature = hash_hmac('sha256', $json . $timestamp, $secret);

        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
        ];
    }
}
