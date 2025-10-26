<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\People;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    /**
     * Kirim OTP
     */
    public function sendOtp(Request $request)
    {
        // Validasi input
        $request->validate([
            'nik' => 'required|string',
            'wa'  => 'required|string',
        ]);

        // Generate hash NIK
        $identityHash = People::generateHmac($request->nik);

        // Normalisasi nomor HP
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
            Log::warning('OTP gagal dikirim: NIK/WA tidak ditemukan', [
                'nik' => $request->nik,
                'wa_input' => $request->wa,
                'wa_normalized' => $normalizedWa,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'NIK atau nomor WhatsApp tidak ditemukan'
            ], 404);
        }

        // Generate OTP 5 digit
        $otp = rand(10000, 99999);

        // Simpan OTP di cache selama 5 menit
        Cache::put('otp_' . $person->id, $otp, now()->addMinutes(5));

        // Log keberhasilan
        Log::info('OTP berhasil dikirim', [
            'person_id' => $person->id,
            'nama' => $person->name ?? '-',
            'nik' => $request->nik,
            'phoneNumber_db' => $person->phoneNumber,
            'wa_input' => $request->wa,
            'wa_normalized' => $normalizedWa,
            'otp' => $otp,
            'expired_at' => now()->addMinutes(5)->toDateTimeString(),
        ]);

        // TODO: Kirim OTP ke WA user (integrasi API WhatsApp)
        // WhatsAppApi::send($person->phoneNumber, "Kode OTP Anda: $otp");

        return response()->json([
            'status'     => 'success',
            'message'    => 'OTP berhasil dikirim',
            'person_id'  => $person->id,
            // 'otp_masked' => substr($otp, 0, 2) . '***' // tidak menampilkan OTP full ke user
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
}
