<?php

use App\Http\Controllers\ActivationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MendagriController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SelfRegistrationController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\SystemLogController;
use Illuminate\Support\Facades\Route;
use App\Services\SecureFileService;
use Illuminate\Support\Facades\Response;
use App\Models\TemporaryPeopleDocument;

Route::get('/admin/activation/data', [ActivationController::class, 'data'])->name('activation.data');

Route::get('/storage/file/{id}', function ($id) {
    $doc = TemporaryPeopleDocument::findOrFail($id);

    if (!\Illuminate\Support\Facades\Storage::exists($doc->encrypted_path)) {
        abort(404, 'File tidak ditemukan');
    }

    $decrypted = SecureFileService::getDecryptedFile($doc->encrypted_path);

    return Response::make($decrypted, 200, [
        'Content-Type' => $doc->mime_type,
        'Content-Disposition' => 'inline; filename="' . $doc->original_name . '"'
    ]);
})->name('storage.file');


// ✅ Rute wilayah dinamis

Route::get('/api/provinces', [MendagriController::class, 'getProvinces']);
Route::get('/api/regencies/{provinceId}', [MendagriController::class, 'getRegencies']);
Route::get('/api/districts/{regencyId}', [MendagriController::class, 'getDistricts']);
Route::get('/api/villages/{districtId}', [MendagriController::class, 'getVillages']);


// ->middleware('check.session:1,2,3')
Route::get('/', [PagesController::class, 'dashboard'])->name('dashboard');
Route::get('addons', [PagesController::class, 'addons'])->name('addons');

Route::get('add-pelanggan/{id?}', [PagesController::class, 'insertPeoples'])->name('customer.create');
Route::get('pelanggan', [PagesController::class, 'peoples'])->name('customer.index');
Route::get('pelanggan/detail/{hash}', [PagesController::class, 'viewsPeople'])->name('customer.view');
Route::get('pelanggan/trx', [PagesController::class, 'viewTransactions'])->name('pelanggan.trx');



Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/account', fn() => view('profile.account'))->name('account');
    Route::get('/notification', fn() => view('profile.notification'))->name('notification');
    Route::get('/security', fn() => view('profile.security'))->name('security');
    Route::get('/apps', fn() => view('profile.apps'))->name('apps');
    Route::get('/privacy', fn() => view('profile.privacy'))->name('privacy');
});

Route::get('transaksi', [PagesController::class, 'transaction'])->name('transaction.index');
Route::get('kategori', [PagesController::class, 'categoryList'])->name('category.index');

Route::get('auth-login', function () { return view('auth.login'); })->name('auth.login');

Route::prefix('user')->group(function () {
    Route::get('/add-user', [PagesController::class, 'userForm'])->name('user.create');
    Route::get('/list', [PagesController::class, 'userList'])->name('user.list');
});

Route::prefix('category')->group(function () {
    Route::get('/category-add', [PagesController::class, 'insertCategory'])->name('category.create');
    Route::get('/', [PagesController::class, 'categoryList'])->name('category.list');
});

Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/me', [LoginController::class, 'me'])->name('me');
Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');

Route::get('/register', [SelfRegistrationController::class, 'showForm'])->name('register.form');
Route::post('/register', [SelfRegistrationController::class, 'submitForm'])->name('register.submit');

Route::get('/verify-otp/{id}', [SelfRegistrationController::class, 'showVerify'])->name('register.verify');
Route::post('/verify-otp/{id}', [SelfRegistrationController::class, 'verifyOtp'])->name('register.verify.submit');
Route::get('/register/resend-otp/{id}', [SelfRegistrationController::class, 'resendOtp'])
    ->name('register.resendOtp');

Route::prefix('activation')->name('activation.')->group(function () {
    Route::get('/', [ActivationController::class, 'index'])->name('index');
    Route::get('/{id}', [ActivationController::class, 'show'])->name('show');
    Route::post('/{id}/activate', [ActivationController::class, 'activate'])->name('activate');
});

