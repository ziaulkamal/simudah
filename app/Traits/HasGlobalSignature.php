<?php

namespace App\Traits;

trait HasGlobalSignature
{
    /**
     * Generate global HMAC signature untuk payload apa pun
     */
    public function generateSignature(array|string $data): string
    {
        $secret = env('APP_KEY');

        if (is_array($data)) {
            ksort($data); // urutkan key agar hasilnya konsisten
            $payload = json_encode($data);
        } else {
            $payload = (string) $data;
        }

        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Verifikasi integritas data dengan signature
     */
    public function verifySignature(array|string $data, string $signature): bool
    {
        return hash_equals(
            $this->generateSignature($data),
            $signature
        );
    }
}
