<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryPeople;
use App\Models\TemporaryPeopleDocument;
use App\Models\People;
use App\Models\PeopleDocument;
use App\Services\SecureFileService;
use Illuminate\Support\Facades\DB;

class SelfRegistrationController extends Controller
{
    /**
     * Tampilkan form registrasi mandiri
     */
    public function showForm()
    {
        return view('public.register');
    }

    /**
     * Proses pendaftaran mandiri
     */
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
            $people = TemporaryPeople::create([
                'fullName' => $request->fullName,
                'identityNumber' => $request->identityNumber,
                'phoneNumber' => $request->phoneNumber,
                'otp_code' => rand(100000, 999999),
                'otp_expires_at' => now()->addMinutes(10),
            ]);

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

            // NOTE: di production, kirim OTP via SMS
            return redirect()->route('register.verify', $people->id)
                ->with('success', 'Pendaftaran berhasil. Masukkan kode OTP berikut untuk verifikasi: ' . $people->otp_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerify($id)
    {
        $temp = TemporaryPeople::findOrFail($id);
        return view('verify-otp', compact('temp'));
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request, $id)
    {
        $request->validate(['otp_code' => 'required|string']);
        $temp = TemporaryPeople::findOrFail($id);

        if ($temp->otp_expires_at < now()) {
            return back()->withErrors(['error' => 'OTP sudah kadaluarsa']);
        }

        if ($temp->otp_code !== $request->otp_code) {
            return back()->withErrors(['error' => 'OTP salah']);
        }

        // Tandai terverifikasi
        $temp->update(['is_verified' => true]);

        // Migrasi ke tabel utama
        $people = People::create([
            'fullName' => $temp->fullName,
            'identityNumber' => $temp->identityNumber,
            'identity_hash' => $temp->identity_hash,
            'phoneNumber' => $temp->phoneNumber,
            'gender' => $temp->gender,
            'birthdate' => $temp->birthdate,
        ]);

        foreach ($temp->documents as $doc) {
            PeopleDocument::create([
                'people_id' => $people->id,
                'type' => $doc->type,
                'original_name' => $doc->original_name,
                'mime_type' => $doc->mime_type,
                'encrypted_path' => $doc->encrypted_path,
            ]);
        }

        $temp->delete();

        return redirect()->route('register.form')->with('success', 'Verifikasi berhasil! Data Anda sudah disimpan.');
    }
}
