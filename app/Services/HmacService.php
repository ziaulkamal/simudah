<?php

namespace App\Services;

class HmacService
{
    /**
     * Generate HMAC SHA256 signature yang sinkron ke Python
     *
     * @param array $body
     * @param string $clientSecret
     * @param int|null $timestamp
     * @return array ['signature' => string, 'timestamp' => int]
     */
    public static function generateSignature(array $body, string $clientSecret, ?int $timestamp = null): array
    {
        $timestamp = $timestamp ?? time();

        // Sortir key agar konsisten
        ksort($body);

        // JSON encode
        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // HMAC: json + timestamp
        $signature = hash_hmac('sha256', $jsonBody . $timestamp, $clientSecret);

        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
        ];
    }
}