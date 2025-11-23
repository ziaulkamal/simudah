<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MendagriController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SecureUserController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;



Route::prefix('mendagri')->middleware(['role.level:99,1'])->group(function () {
    Route::post('/identity/nik', [MendagriController::class, 'fetchIdentityByNik'])->middleware('verify.app');
    Route::post('/identity/search', [MendagriController::class, 'fetchIdentityBySearch'])->middleware('verify.app');
});

Route::prefix('people')->group(function () {
    Route::post('/', [PeopleController::class, 'store'])->middleware(['role.level:99,1'])->name('people.store');
    Route::get('/hash/{identity_hash}', [PeopleController::class, 'showByHash'])->middleware(['role.level:99,1,2,3'])->name('people.get');
    Route::put('/{id}', [PeopleController::class, 'update'])->middleware(['role.level:99,1'])->name('people.update');
    Route::delete('/{id}', [PeopleController::class, 'destroy'])->middleware(['role.level:99,1'])->name('people.delete');
    Route::post('/{id}/location', [PeopleController::class, 'updateLocation'])->middleware(['role.level:99,1,2,3']);
    Route::post('/{id}/reactivate', [PeopleController::class, 'reactivateAccount'])->middleware(['role.level:99']);

    // Tambahan route untuk kategori
    Route::post('/{id}/assign-category', [PeopleController::class, 'assignCategory'])->middleware(['role.level:99,1'])->name('people.assignCategory');
    Route::get('/{id}/category-change-count', [PeopleController::class, 'categoryChangeCount'])->middleware(['role.level:99,1']);

    // 🔹 Tambahan route untuk close akun (nonaktifkan pelanggan)
    Route::patch('/{id}/close-account', [PeopleController::class, 'closeAccount'])->middleware(['role.level:99,1'])->name('people.closeAccount');
});
Route::get('/lsignature/{nik}', [PeopleController::class, 'localSignature']);

Route::prefix('transactions')->middleware(['role.level:99,1'])->group(function () {
    Route::get('/all', [TransactionController::class, 'all'])->middleware(['ajax.same.origin', 'role.level:99,1,']); // 🔹 semua transaksi (global list)
    Route::get('/{id}/people', [TransactionController::class, 'index'])->middleware(['ajax.same.origin', 'role.level:99,1,2']); // semua transaksi by people
    Route::get('/{id}', [TransactionController::class, 'show'])->middleware(['ajax.same.origin', 'role.level:99,1,2']); // detail transaksi by id
    Route::get('/pelanggan/{hash}', [TransactionController::class, 'searchHashIdentity'])->middleware(['ajax.same.origin','role.level:99,1,2']); // detail transaksi by id
    Route::post('/{id}/pay', [PaymentController::class, 'payTransaction'])->middleware(['ajax.same.origin']); // bayar transaksi
});

Route::prefix('accounts')->controller(SecureUserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::post('/users', 'store');
    Route::get('/users/{id}/edit', 'edit');
    Route::put('/users/{id}', 'update');
    Route::delete('/users/{id}', 'destroy');
});
/*
|--------------------------------------------------------------------------
| Category Routes via GlobalController
|--------------------------------------------------------------------------
*/
Route::prefix('categories')->controller(CategoryController::class)->group(function () {
    Route::get('/', 'index');       // GET /api/categories
    Route::post('/', 'store')->name('categories.store');      // POST /api/categories
    Route::get('/{id}', 'edit');    // GET /api/categories/{id}
    Route::put('/{id}', 'update');  // PUT /api/categories/{id}
    Route::delete('/{id}', 'destroy'); // DELETE /api/categories/{id}
});

Route::get('/logs', [SystemLogController::class, 'index']);
Route::get('/me', [LoginController::class, 'me'])->middleware(['ajax.same.origin'])->name('me');

Route::middleware(['ajax.same.origin'])->group(function () {
    Route::get('/provinces', [MendagriController::class, 'getProvinces']);
    Route::get('/regencies/{provinceId}', [MendagriController::class, 'getRegencies']);
    Route::get('/districts/{regencyId}', [MendagriController::class, 'getDistricts']);
    Route::get('/villages/{districtId}', [MendagriController::class, 'getVillages']);
});
Route::get('signature', [SignatureController::class, 'signatures'])->name('signature');
