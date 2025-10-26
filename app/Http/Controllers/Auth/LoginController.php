<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\People;
use App\Models\SecureUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Endpoint login utama
     * Bisa login via NIK+HP (People) atau Username+Password (SecureUser)
     */
    public function login(Request $request)
    {
        if ($request->filled('identityNumber') && $request->filled('phoneNumber')) {
            return $this->loginWithIdentity($request);
        }

        if ($request->filled('username') && $request->filled('password')) {
            return $this->loginWithUsername($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Missing login credentials',
        ], 422);
    }

    /**
     * Login menggunakan NIK + phoneNumber
     */
    protected function loginWithIdentity(Request $request)
    {
        $request->validate([
            'identityNumber' => 'required|string',
            'phoneNumber'    => 'required|string',
        ]);

        $identityHash = People::generateHmac($request->identityNumber);

        $person = People::with(['role', 'district', 'village'])
            ->where('identity_hash', $identityHash)
            ->where('phoneNumber', $request->phoneNumber)
            ->first();

        if (!$person) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identity number or phone number is incorrect',
            ], 401);
        }

        $this->createSession($person->id, 'people', $person->role_id, remember: $request->boolean('remember'));

        Log::info('Login success (People)', [
            'person_id' => $person->id,
            'fullName'  => $person->fullName,
            'phone'     => $person->phoneNumber,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful (identity)',
            'data'    => [
                'id'        => $person->id,
                'fullName'  => $person->fullName,
                'role'      => $person->role?->name,
                'level'     => $person->role?->level,
                'phone'     => $person->phoneNumber,
                'district'  => $person->district?->name,
                'village'   => $person->village?->name,
            ],
        ]);
    }

    /**
     * Login menggunakan username + password
     */
    protected function loginWithUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = SecureUser::with(['role', 'people.district', 'people.village'])
            ->where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau password salah',
            ], 401);
        }

        // ✅ Jika user belum punya People, buat data sementara di session saja
        if (!$user->people) {
            $tempPeople = [
                'id'        => 'temp-' . $user->id,
                'fullName'  => 'Temporary User ' . strtoupper(Str::random(4)),
                'phoneNumber' => 'TEMP-' . time(),
                'role_id'   => $user->role_id,
            ];

            // Simpan ke session (bukan DB)
            Session::put('temp_people', $tempPeople);
        }

        // Ambil ID login (pakai people_id jika ada, atau user_id jika tidak)
        $loginId = $user->people_id ?? $user->id;
        $this->createSession($loginId, 'secure', $user->role_id, remember: $request->boolean('remember'));

        Log::info('Login success (SecureUser)', [
            'user_id'   => $user->id,
            'username'  => $user->username,
            'role'      => $user->role?->name,
            'people_id' => $user->people_id,
        ]);

        // Ambil data people, bisa dari DB atau session temp
        $peopleData = $user->people
            ? [
                'id'        => $user->people->id,
                'name'      => $user->people->fullName,
                'phone'     => $user->people->phoneNumber,
                'district'  => $user->people->district?->name,
                'village'   => $user->people->village?->name,
            ]
            : Session::get('temp_people');

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful (user)',
            'data'    => [
                'id'        => $user->id,
                'username'  => $user->username,
                'role'      => $user->role?->name,
                'level'     => $user->role?->level,
                'people'    => $peopleData,
            ],
        ]);
    }


    /**
     * Buat session login (support remember me)
     */
    protected function createSession($loginId, $type = 'people', $roleId = null, bool $remember = false)
    {
        Session::put('login_id', $loginId);
        Session::put('login_type', $type);
        Session::put('role_id', $roleId);
        Session::put('login_time', now());
        Session::put('remember_me', $remember);
        Session::put('expires_at', $remember ? now()->addDays(7) : now()->addHours(2));

        Log::info('Session created', [
            'login_id' => $loginId,
            'role_id' => $roleId,
            'expires_at' => Session::get('expires_at'),
            'session_id' => Session::getId(),
        ]);
    }


    /**
     * Dapatkan data user yang sedang login
     */
    public function me()
    {
        if (!Session::has('login_id')) {
            return response()->json(['status' => 'error', 'message' => 'Not logged in'], 401);
        }

        if (Session::get('login_type') === 'secure') {
            $user = SecureUser::with(['role', 'people.district', 'people.village'])
                ->find(Session::get('login_id'));
        } else {
            $user = People::with(['role', 'district', 'village'])
                ->find(Session::get('login_id'));
        }

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        Session::flush();
        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
