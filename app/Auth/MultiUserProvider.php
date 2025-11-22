<?php

namespace App\Auth;

use App\Models\SecureUser;
use App\Models\People;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class MultiUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return SecureUser::find($identifier)
            ?? People::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return SecureUser::where('remember_token', $token)->find($identifier)
            ?? People::where('remember_token', $token)->find($identifier);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        if (method_exists($user, 'setRememberToken')) {
            $user->setRememberToken($token);
        }

        if (method_exists($user, 'save')) {
            $user->save();
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        // Login via username → SecureUser
        if (isset($credentials['username'])) {
            return SecureUser::where('username', $credentials['username'])->first();
        }

        // Login via identity hash (People)
        if (isset($credentials['identity_hash'])) {
            return People::where('identity_hash', $credentials['identity_hash'])->first();
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // SecureUser pakai password
        if ($user instanceof SecureUser && isset($credentials['password'])) {
            return Hash::check($credentials['password'], $user->password);
        }

        // People tidak pakai password (OTP / phone)
        return true;
    }

    /**
     * Laravel 12 required:
     * Rehash password if needed.
     *
     * Karena SecureUser pakai hash password,
     * dan People tidak memakai password,
     * maka kita hanya rehash jika instance SecureUser.
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if (!($user instanceof SecureUser)) {
            return;
        }

        if (!isset($credentials['password'])) {
            return;
        }

        // Jika perlu rehash
        if ($force || Hash::needsRehash($user->password)) {
            $user->password = Hash::make($credentials['password']);
            $user->save();
        }
    }
}
