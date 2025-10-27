<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\TemporaryPeople;
use App\Models\TemporaryPeopleDocument;
use App\Services\SecureFileService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $identityHash = hash_hmac('sha256', $request->identityNumber, env('APP_KEY'));
            $normalizedPhone = $this->normalizePhone($request->phoneNumber);

            // 1️⃣ Cek data di TemporaryPeople
            $existsTemp = TemporaryPeople::where('identity_hash', $identityHash)
                ->orWhere('phoneNumber', $normalizedPhone)
                ->first();

            // 2️⃣ Cek data di tabel utama People
            $existsMain = People::where('identity_hash', $identityHash)
                ->orWhere('phoneNumber', $normalizedPhone)
                ->first();

            if ($existsMain) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIK atau Nomor HP sudah terdaftar di sistem.'
                ]);
            }

            // 🧩 Jika sudah ada di temporary tapi belum verifikasi → kirim ulang OTP
            if ($existsTemp && !$existsTemp->is_verified) {
                $otp = rand(10000, 99999);

                $existsTemp->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]);

                $this->pushWhatsApp($otp, $normalizedPhone);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Akun Anda sudah terdaftar namun belum terverifikasi. Kami telah mengirim ulang kode OTP.',
                    'redirect' => route('register.verify', ['id' => Crypt::encryptString($existsTemp->id)])
                ]);
            }

            // 🚫 Jika sudah terverifikasi
            if ($existsTemp && $existsTemp->is_verified) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIK atau Nomor HP sudah terdaftar dan terverifikasi.'
                ]);
            }

            // 🆕 Data baru
            $otp = rand(10000, 99999);

            $people = TemporaryPeople::create([
                'fullName' => $request->fullName,
                'identityNumber' => $request->identityNumber,
                'identity_hash' => $identityHash,
                'phoneNumber' => $normalizedPhone,
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
                'is_verified' => false,
            ]);

            $this->pushWhatsApp($otp, $normalizedPhone);

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
                'message' => 'Pendaftaran berhasil. Kode OTP telah dikirim ke WhatsApp Anda.',
                'redirect' => route('register.verify', ['id' => Crypt::encryptString($people->id)])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('submitForm error', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
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
        $request->validate(['otp_code' => 'required|string']);
        $temp = TemporaryPeople::findOrFail($id);

        if ($temp->otp_expires_at < now()) {
            return response()->json(['status' => 'error', 'message' => 'OTP sudah kadaluarsa.'], 400);
        }

        if ($temp->otp_code !== $request->otp_code) {
            return response()->json(['status' => 'error', 'message' => 'OTP salah.'], 400);
        }

        $temp->update(['is_verified' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil. Data Anda akan dikonfirmasi oleh petugas.',
        ]);
    }

    public function resendOtp($encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
            $person = TemporaryPeople::find($id);

            if (!$person) {
                return response()->json(['status' => 'error', 'message' => 'Data pendaftar tidak ditemukan.'], 404);
            }

            if ($person->is_verified) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun sudah terverifikasi. Silakan login.',
                    'redirect' => route('login')
                ]);
            }

            $otp = rand(10000, 99999);

            $person->update([
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            $this->pushWhatsApp($otp, $person->phoneNumber);

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP baru telah dikirim ke WhatsApp Anda.',
                'redirect' => route('register.verify', ['id' => $encryptedId])
            ]);
        } catch (\Exception $e) {
            Log::error('resendOtp error', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '62')) $phone = '0' . substr($phone, 2);
        if (str_starts_with($phone, '8')) $phone = '0' . $phone;
        return $phone;
    }

    /**
     * Kirim OTP via WhatsApp (pakai Guzzle)
     */
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
