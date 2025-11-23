<?php

namespace App\Services;

use App\Models\People;
use App\Models\SecureUser;
use Illuminate\Support\Facades\Session;

class AuthService
{
    /**
     * Buat session universal untuk 4 kondisi login
     */
    public function makeUnifiedSession($authType, $secureUser = null, $people = null)
    {
        $session = [
            'auth_type' => $authType,
            'secure_user' => null,
            'people' => null,
        ];

        if ($secureUser) {
            $session['secure_user'] = [
                'id'        => $secureUser->id,
                'username'  => $secureUser->username,
                'role'      => $secureUser->role?->name,
                'level'     => $secureUser->role?->level,
                'people_id' => $secureUser->people_id,
            ];
        }

        if ($people) {
            $session['people'] = [
                'id'        => $people->id,
                'fullName'  => $people->fullName,
                'nik'       => $people->identityNumber,
                'phone'     => $people->phoneNumber,
                'district'  => $people->district?->name,
                'village'   => $people->village?->name,
                'role'      => $people->role?->name,
                'level'     => $people->role?->level,
            ];

            // ✅ TAMBAHKAN INI!
            Session::put('people.id', $people->id);
        }

        // PRIORITAS LABEL PROFILE
        Session::put(
            'login_name',
            $session['people']['fullName']
                ?? $session['secure_user']['username']
                ?? 'Guest'
        );

        // PRIORITAS LEVEL & ROLE
        Session::put(
            'role_name',
            $session['secure_user']['role']
                ?? $session['people']['role']
                ?? 'Pelanggan'
        );

        Session::put(
            'role_level',
            $session['secure_user']['level']
                ?? $session['people']['level']
                ?? 99
        );

        // Waktu expired 2 jam
        Session::put('expires_at', now()->addHours(2));

        // Tambah signature session
        $sig = $this->generateSignatureSession();
        Session::put('signature_session', $sig);

        return [
            'session' => $session,
            'signature_session' => $sig,
        ];
    }


    public function generateSignatureSession()
    {
        return [
            'token'     => bin2hex(random_bytes(32)),
            'timestamp' => time()
        ];
    }
}
