<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MendagriController;

Route::prefix('mendagri')->group(function () {
    Route::post('/identity/nik', [MendagriController::class, 'fetchIdentityByNik'])->middleware('verify.app');
    Route::post('/identity/search', [MendagriController::class, 'fetchIdentityBySearch']);
});
