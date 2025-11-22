<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\SecureUser;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }
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

        $hash = People::generateHmac($request->identityNumber);

        $person = People::with(['role', 'district', 'village'])
            ->where('identity_hash', $hash)
            ->where('phoneNumber', $request->phoneNumber)
            ->first();

        if (!$person) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identity number or phone number is incorrect',
            ], 401);
        }

        // Cek apakah People memiliki SecureUser
        $secure = SecureUser::with('role')->where('people_id', $person->id)->first();

        // Gunakan unified session builder
        $session = $this->auth->makeUnifiedSession(
            authType: 'people',
            secureUser: $secure,
            people: $person
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful (identity)',
            'data' => $session
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

        // Jika SecureUser punya relasi People, ikutkan
        $person = $user->people;

        // Buat unified session + signature_session
        $result = $this->auth->makeUnifiedSession(
            authType: 'secure_user',
            secureUser: $user,
            people: $person
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful (user)',
            'data'    => $result['session'],
            'signature_session' => $result['signature_session'],
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
            return response()->json(session()->all(), 401);
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
