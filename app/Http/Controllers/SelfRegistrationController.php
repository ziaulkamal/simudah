<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\OtpController;
use App\Models\People;
use App\Models\PeopleDocument;
use App\Models\TemporaryPeople;
use App\Models\TemporaryPeopleDocument;
use App\Services\SecureFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\This;

class SelfRegistrationController extends Controller
{
    public function showForm()
    {
        return view('public.register');
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'identityNumber' => 'required|string|min:10|max:30',
            'phoneNumber' => 'required|string|max:15',
            'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $existsTemp = TemporaryPeople::where('identity_hash', hash_hmac('sha256', $request->identityNumber, env('APP_KEY')))
                ->orWhere('phoneNumber', $request->phoneNumber)
                ->first();

            if ($existsTemp) {
                return response()->json(['status' => 'error', 'message' => 'NIK atau Nomor HP sudah terdaftar di pendaftaran mandiri sebelumnya.']);
            }

            $existsMain = People::where('identity_hash', hash_hmac('sha256', $request->identityNumber, env('APP_KEY')))
                ->orWhere('phoneNumber', $request->phoneNumber)
                ->first();

            if ($existsMain) {
                return response()->json(['status' => 'error', 'message' => 'NIK atau Nomor HP sudah terdaftar di sistem.']);
            }

            $otp = rand(10000, 99999);

            $people = TemporaryPeople::create([
                'fullName' => $request->fullName,
                'identityNumber' => $request->identityNumber,
                'phoneNumber' => $this->normalizePhone($request->phoneNumber),
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            $this->pushWhatsApp($otp, $request->phoneNumber);

            $file = $request->file('ktp_file');
            $encryptedPath = SecureFileService::storeEncryptedFile($file);

            TemporaryPeopleDocument::create([
                'temporary_people_id' => $people->id,
                'type' => 'ktp',
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'encrypted_path' => $encryptedPath,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pendaftaran berhasil',
                'redirect' => route('register.verify', ['id' => Crypt::encryptString($people->id)])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function showVerify($id)
    {
        $decryptedId = Crypt::decryptString($id);
        $temp = TemporaryPeople::findOrFail($decryptedId);
        return view('public.verify', compact('temp'));
    }

    public function verifyOtp(Request $request, $id)
    {
        $request->validate([
            'otp_code' => 'required|string',
        ]);

        $temp = TemporaryPeople::findOrFail($id);

        if ($temp->otp_expires_at < now()) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP sudah kadaluarsa.'
            ], 400);
        }

        if ($temp->otp_code !== $request->otp_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP salah.'
            ], 400);
        }

        $temp->update(['is_verified' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil. Data yang anda berikan akan dilakukan konfirmasi lebih lanjut oleh petugas kami.',
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
            $text = "🔐 Kode OTP Anda adalah *{$otp}*.\n\nJangan berikan kode ini kepada siapa pun. #SIMUDAH";

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
