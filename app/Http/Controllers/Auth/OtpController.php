<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\SecureUser;
use App\Services\AuthService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }
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

        if (!$person) {

            return response()->json([
                'status'  => 'error',
                'message' => 'NIK atau nomor WhatsApp tidak ditemukan'
            ], 404);
        }

        // Generate OTP 5 digit
        $existingOtp = Cache::get('otp_' . $person->id);

        if ($existingOtp) {
            $otp = $existingOtp; // gunakan OTP lama
        } else {
            // Generate OTP baru hanya jika belum ada
            $otp = rand(10000, 99999);
            Cache::put('otp_' . $person->id, $otp, now()->addMinutes(5));
        }

        // TODO: Kirim OTP ke WA user (integrasi API WhatsApp)
        $this->pushWhatsApp($otp, $person->phoneNumber);
        // WhatsAppApi::send($person->phoneNumber, "Kode OTP Anda: $otp");

        return response()->json([
            'status'     => 'success',
            'message'    => 'OTP berhasil dikirim',
            'person_id'  => $person->id,
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
        $person = People::with('role')->find($request->person_id);

        // Cari secureUser jika ada relasi
        $secure = SecureUser::with('role')->where('people_id', $person->id)->first();
        // Log::info('verifyOtp compare', ['person_id' => $request->person_id, 'otp_received' => $otp, 'otp_cached' => $cachedOtp]);

        if (!$cachedOtp || (string)$cachedOtp !== (string)$otp) {

            return response()->json([
                'status' => 'error',
                'message' => 'OTP salah atau sudah kadaluarsa'
            ], 401);
        }
        // Session builder
        $result = app(AuthService::class)->makeUnifiedSession(
            authType: 'people',
            secureUser: $secure,
            people: $person
        );

        Cache::forget('otp_' . $request->person_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => $result['session'],
            'signature_session' => $result['signature_session']
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
        $client = new Client([
            'timeout' => 20, // 20 detik timeout
            'connect_timeout' => 10,
            'http_errors' => false,
        ]);

        try {
            $normalized = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (str_starts_with($normalized, '0')) {
                $normalized = '62' . substr($normalized, 1);
            }
            $chatId = $normalized . '@c.us';
            $text = "🔐 Kode OTP Anda adalah *{$otp}*.\n\nJangan berikan kode ini kepada siapa pun. #SIMUDAH";

            $response = $client->post(env('WA_GATEWAY_URL'), [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-Api-Key' => env('WA_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'chatId' => $chatId,
                    'reply_to' => null,
                    'text' => $text,
                    'linkPreview' => false,
                    'linkPreviewHighQuality' => false,
                    'session' => 'default',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                Log::info('OTP terkirim ke WhatsApp', [
                    'phone' => $phoneNumber,
                    'chatId' => $chatId,
                    'otp' => $otp,
                ]);
                return true;
            }

            Log::error('Gagal kirim OTP via WhatsApp', [
                'status' => $response->getStatusCode(),
                'body' => (string) $response->getBody(),
                'phone' => $phoneNumber,
            ]);
            return false;
        } catch (RequestException $e) {
            Log::error('pushWhatsApp RequestException', [
                'message' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('pushWhatsApp General Error', [
                'message' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);
            return false;
        }
    }
}
