<?php

use App\Http\Controllers\MendagriController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SecureUserController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;





Route::prefix('mendagri')->group(function () {
    Route::post('/identity/nik', [MendagriController::class, 'fetchIdentityByNik'])->middleware('verify.app');
    Route::post('/identity/search', [MendagriController::class, 'fetchIdentityBySearch'])->middleware('verify.app');
});

Route::prefix('people')->group(function () {
    Route::post('/', [PeopleController::class, 'store'])->name('people.store');
    Route::get('/hash/{identity_hash}', [PeopleController::class, 'showByHash'])->name('people.get');
    Route::get('/normal/{nik}', [PeopleController::class, 'testNik']);
    Route::put('/{id}', [PeopleController::class, 'update'])->name('people.update');
    Route::delete('/{id}', [PeopleController::class, 'destroy'])->name('people.delete');
    Route::post('/{id}/location', [PeopleController::class, 'updateLocation']);
    Route::post('/{id}/reactivate', [PeopleController::class, 'reactivateAccount']);

    // Tambahan route untuk kategori
    Route::post('/{id}/assign-category', [PeopleController::class, 'assignCategory'])->name('people.assignCategory');
    Route::get('/{id}/category-change-count', [PeopleController::class, 'categoryChangeCount']);

    // 🔹 Tambahan route untuk close akun (nonaktifkan pelanggan)
    Route::patch('/{id}/close-account', [PeopleController::class, 'closeAccount'])->name('people.closeAccount');
});
Route::get('/lsignature/{nik}', [PeopleController::class, 'localSignature']);

Route::prefix('transactions')->group(function () {
    Route::get('/all', [TransactionController::class, 'all']); // 🔹 semua transaksi (global list)
    Route::get('/{id}/people', [TransactionController::class, 'index']); // semua transaksi by people
    Route::get('/{id}', [TransactionController::class, 'show']); // detail transaksi by id
    Route::get('/pelanggan/{hash}', [TransactionController::class, 'searchHashIdentity']); // detail transaksi by id
    Route::post('/{id}/pay', [PaymentController::class, 'payTransaction']); // bayar transaksi
});

Route::prefix('secure-users')->group(function () {
    Route::get('/', [SecureUserController::class, 'index']);
    Route::post('/', [SecureUserController::class, 'store'])->name('secureuser.store');
    Route::get('{id}', [SecureUserController::class, 'show']);
    Route::put('{id}', [SecureUserController::class, 'update']);
    Route::patch('{id}', [SecureUserController::class, 'update']);
    Route::delete('{id}', [SecureUserController::class, 'destroy']);
});

Route::get('signature', [SignatureController::class, 'signatures'])->name('signature');