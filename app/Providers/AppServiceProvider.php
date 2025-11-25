<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        view()->composer('*', function ($view) {
            $view->with('loginName', session('login_name'));
            $view->with('roleName', session('role_name'));
            $view->with('signatureSession', session('signature_session'));
        });
    }
}
