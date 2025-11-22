<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\MultiUserProvider;

class MultiAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('multi', function ($app, array $config) {
            return new MultiUserProvider();
        });
    }
}
