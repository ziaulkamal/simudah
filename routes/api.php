<?php

use App\Http\Controllers\MendagriController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;


Route::prefix('mendagri')->group(function () {
    Route::post('/identity/nik', [MendagriController::class, 'fetchIdentityByNik'])->middleware('verify.app');
    Route::post('/identity/search', [MendagriController::class, 'fetchIdentityBySearch'])->middleware('verify.app');
});

Route::prefix('people')->group(function () {
    Route::post('/', [PeopleController::class, 'store'])->name('people.store');
    Route::get('/hash/{identity_hash}', [PeopleController::class, 'showByHash'])->name('people.get'); // ambil by hash
    Route::get('/normal/{nik}', [PeopleController::class, 'testNik']); // ambil by hash
    Route::put('/{id}', [PeopleController::class, 'update'])->name('people.update');
    Route::delete('/{id}', [PeopleController::class, 'destroy'])->name('people.delete');
});

Route::get('signature', [SignatureController::class, 'signatures'])->name('signature');