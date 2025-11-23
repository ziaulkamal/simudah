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
use App\Models\TemporaryPeopleDocument;
use App\Services\SecureFileService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


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



// ->middleware(['auth.check', 'role.level:99'])
Route::get('/', [PagesController::class, 'dashboard'])->middleware(['auth.check'])->name('dashboard');
Route::get('addons', [PagesController::class, 'addons'])->middleware(['auth.check', 'role.level:99,1'])->name('addons');

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

Route::get('auth-login', function () {
    return view('auth.login');
})->middleware('auth.accept')->name('auth.login');

Route::prefix('user')->middleware(['auth.check', 'role.level:99,1'])->group(function () {
    Route::get('/add-user', [PagesController::class, 'userForm'])->name('user.create');
    Route::get('/list', [PagesController::class, 'userList'])->name('user.list');
});

Route::prefix('category')->middleware(['auth.check', 'role.level:99'])->group(function () {
    Route::get('/category-add', [PagesController::class, 'insertCategory'])->name('category.create');
    Route::get('/', [PagesController::class, 'categoryList'])->name('category.index');
});

Route::post('/send-otp', [OtpController::class, 'sendOtp'])->middleware('auth.accept')->name('send.otp');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->middleware('auth.accept')->name('verify.otp');
Route::post('/login', [LoginController::class, 'login'])->middleware('auth.accept')->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth.check', 'role.level:99,1,2,3,4'])->name('logout');
Route::get('/me', [LoginController::class, 'me'])->middleware(['auth.check', 'role.level:99'])->name('me');
Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');

Route::get('/register', [SelfRegistrationController::class, 'showForm'])->middleware('auth.accept')->name('register.form');
Route::post('/register', [SelfRegistrationController::class, 'submitForm'])->middleware('auth.accept')->name('register.submit');

Route::get('/verify-otp/{id}', [SelfRegistrationController::class, 'showVerify'])->middleware('auth.accept')->name('register.verify');
Route::post('/verify-otp/{id}', [SelfRegistrationController::class, 'verifyOtp'])->middleware('auth.accept')->name('register.verify.submit');
Route::get('/register/resend-otp/{id}', [SelfRegistrationController::class, 'resendOtp'])->middleware('auth.accept')
    ->name('register.resendOtp');

Route::prefix('activation')->middleware(['auth.check', 'role.level:99,1'])->name('activation.')->group(function () {
    Route::get('/', [ActivationController::class, 'index'])->name('index');
    Route::get('/{id}', [ActivationController::class, 'show'])->name('show');
    Route::post('/{id}/activate', [ActivationController::class, 'activate'])->name('activate');
});

