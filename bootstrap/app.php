<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verify.app'    => \App\Http\Middleware\VerifyAppAccess::class,
            'check.session' => \App\Http\Middleware\CheckLoginSession::class,
            'ajax.same.origin' => \App\Http\Middleware\AjaxSameOrigin::class,
            'verify.api.signature' => \App\Http\Middleware\VerifyApiSignature::class,
            'role.level' => \App\Http\Middleware\CheckRoleLevel::class,
            'auth.check' => \App\Http\Middleware\CheckLogin::class,
            'auth.accept' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    // ✅ REGISTER PROVIDER DENGAN CARA RESMI
    ->withProviders([
        App\Providers\MultiAuthServiceProvider::class,
    ])

    ->create();
