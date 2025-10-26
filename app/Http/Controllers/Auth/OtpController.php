<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\People;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\OtpLog;

class OtpController extends Controller
{
    /**
     * Kirim OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'wa'  => 'required|string',
        ]);

        $identityHash = People::generateHmac($request->nik);
        $normalizedWa = $this->normalizePhone($request->wa);

        // Cari user berdasarkan NIK dan variasi nomor WA
        $person = People::where('identity_hash', $identityHash)
            ->where(function ($q) use ($normalizedWa) {
                $q->where('phoneNumber', $normalizedWa)
                    ->orWhere('phoneNumber', '62' . substr($normalizedWa, 1))
                    ->orWhere('phoneNumber', '+62' . substr($normalizedWa, 1));
            })
            ->first();

        // ❌ Jika tidak ditemukan
        if (!$person) {
            OtpLog::create([
                'nik' => $request->nik,
                'phone' => $request->wa,
                'status' => 'not_found',
                'message' => 'NIK atau nomor WhatsApp tidak ditemukan',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'NIK atau nomor WhatsApp tidak ditemukan'
            ], 404);
        }

        $phone = $person->phoneNumber;

        // 🔒 Anti-Spam (pakai nomor HP sebagai key)
        $attemptKey = 'otp_attempts_' . $phone;
        $blockedKey = 'otp_blocked_' . $phone;

        if (Cache::has($blockedKey)) {
            OtpLog::create([
                'nik' => $request->nik,
                'phone' => $phone,
                'status' => 'blocked',
                'message' => 'Diblokir sementara selama 30 menit',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Terlalu banyak percobaan OTP. Silakan coba lagi dalam 30 menit.'
            ], 429);
        }

        $attempts = Cache::get($attemptKey, 0) + 1;
        Cache::put($attemptKey, $attempts, now()->addMinutes(30));

        if ($attempts > 10) {
            Cache::put($blockedKey, now()->addMinutes(30), now()->addMinutes(30));
            Cache::forget($attemptKey);

            OtpLog::create([
                'nik' => $request->nik,
                'phone' => $phone,
                'status' => 'blocked',
                'message' => 'Melebihi batas 10x permintaan dalam 30 menit',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Terlalu banyak percobaan OTP. Akun Anda diblokir selama 30 menit.'
            ], 429);
        }

        // 🔢 Gunakan OTP lama jika masih valid
        $otp = Cache::remember('otp_' . $phone, now()->addMinutes(5), function () {
            return rand(10000, 99999);
        });

        // Kirim OTP via WhatsApp
        $success = $this->pushWhatsApp($otp, $phone);

        // 🧾 Catat ke tabel otp_logs
        OtpLog::create([
            'phone' => $phone,
            'otp' => $otp,
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'OTP terkirim ke WhatsApp' : 'Gagal mengirim OTP ke WhatsApp',
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'status'     => 'success',
            'message'    => 'OTP berhasil dikirim',
            'phone'      => $phone,
            'attempts'   => $attempts,
        ]);
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {

        $otp = $request->input('otp', $request->input('otp_code', null));

        // Normalisasi: string, hapus non-digit
        $otp = is_null($otp) ? null : preg_replace('/\D/', '', (string) $otp);

        // Validasi manual (agar pesan error bisa lebih jelas)
        if (!$request->has('person_id') || !is_numeric($request->person_id)) {
            // Log::warning('verifyOtp missing person_id', ['person_id' => $request->person_id ?? null]);
            return response()->json(['status' => 'error', 'message' => 'person_id required'], 422);
        }
        if (empty($otp) || strlen($otp) !== 5) {
            // Log::warning('verifyOtp invalid otp', ['person_id' => $request->person_id, 'otp_received' => $request->input('otp')]);
            return response()->json(['status' => 'error', 'message' => 'OTP harus 5 digit'], 422);
        }

        $cachedOtp = Cache::get('otp_' . $request->person_id);

        // Log::info('verifyOtp compare', ['person_id' => $request->person_id, 'otp_received' => $otp, 'otp_cached' => $cachedOtp]);

        if (!$cachedOtp || (string)$cachedOtp !== (string)$otp) {

            return response()->json([
                'status' => 'error',
                'message' => 'OTP salah atau sudah kadaluarsa'
            ], 401);
        }

        // OTP valid → buat session 2 jam
        Session::put('login_id', $request->person_id);
        Session::put('login_type', 'people');
        Session::put('role_id', $person->role_id ?? 3); // 👈 default role pelanggan (misal 3)
        Session::put('login_time', now());
        Session::put('expires_at', now()->addHours(2));

        // Hapus OTP dari cache
        Cache::forget('otp_' . $request->person_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil'
        ]);
    }

    /**
     * Normalisasi format nomor HP agar konsisten
     */
    private function normalizePhone($phone)
    {
        // Hapus spasi, tanda plus, dan karakter non-angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 62 tapi database banyak pakai 08 → ubah jadi 08
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        // Jika diawali 8 (tanpa 0), tambahkan 0
        if (str_starts_with($phone, '8')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    private function pushWhatsApp($otp, $phoneNumber)
    {
        try {
            // Normalisasi nomor ke format internasional (misal: 628123456789)
            $normalized = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (str_starts_with($normalized, '0')) {
                $normalized = '62' . substr($normalized, 1);
            }

            // Format chatId sesuai API (contoh: 628123456789@c.us)
            $chatId = $normalized . '@c.us';

            // Pesan OTP
            $text = "🔐 Kode OTP Anda adalah *{$otp}*.\n\nJangan berikan kode ini kepada siapa pun. Berlaku selama 5 menit.";

            // Panggil API WhatsApp
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WA_API_KEY', 'c386cfb98aed431787816f6b957df354'),
                'Content-Type' => 'application/json',
            ])->post(env('WA_GATEWAY_URL', 'http://wagateway:3000/api/sendText'), [
                'chatId' => $chatId,
                'reply_to' => null,
                'text' => $text,
                'linkPreview' => false,
                'linkPreviewHighQuality' => false,
                'session' => 'default',
            ]);

            if ($response->successful()) {
                Log::info('OTP terkirim ke WhatsApp', [
                    'phone' => $phoneNumber,
                    'chatId' => $chatId,
                    'otp' => $otp,
                ]);
                return true;
            } else {
                Log::error('Gagal mengirim OTP ke WhatsApp', [
                    'phone' => $phoneNumber,
                    'chatId' => $chatId,
                    'otp' => $otp,
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            Log::error('pushWhatsApp error', [
                'message' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);
            return false;
        }
    }
}
