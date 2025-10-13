<?php

namespace App\Services;

class HmacService
{
    public static function generateSignature(array $body, string $clientSecret): string
    {
        // Bangun string seperti Python dict: {'key': 'value'}
        $pairs = [];
        foreach ($body as $key => $value) {
            $pairs[] = "'{$key}': '{$value}'";
        }
        $bodyStr = '{' . implode(', ', $pairs) . '}';

        // Hitung HMAC
        return hash_hmac('sha256', $bodyStr, $clientSecret);
    }
}
