<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'secure_users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [

        // WEB GUARD
        'web' => [
            'driver' => 'session',
            'provider' => 'multi_users',
        ],

        // API GUARD
        'api' => [
            'driver' => 'token',
            'provider' => 'multi_users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [

        // Provider asli — tidak harus dipakai
        'secure_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\SecureUser::class,
        ],

        'people' => [
            'driver' => 'eloquent',
            'model'  => App\Models\People::class,
        ],

        /**
         * PROVIDER KUSTOM
         * Tidak membutuhkan 'model' karena menggunakan MultiUserProvider
         * untuk membaca ke dua tabel (SecureUser + People).
         */
        'multi_users' => [
            'driver' => 'multi',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset (Jika Anda pakai reset password via SecureUser)
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'secure_users' => [
            'provider' => 'secure_users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],
];
