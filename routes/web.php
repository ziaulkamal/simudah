<?php

use App\Http\Controllers\MendagriController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

// ✅ Rute wilayah dinamis

Route::get('/api/provinces', [MendagriController::class, 'getProvinces']);
Route::get('/api/regencies/{provinceId}', [MendagriController::class, 'getRegencies']);
Route::get('/api/districts/{regencyId}', [MendagriController::class, 'getDistricts']);
Route::get('/api/villages/{districtId}', [MendagriController::class, 'getVillages']);



Route::get('/', [PagesController::class, 'dashboard'])->name('dashboard');
Route::get('addons', [PagesController::class, 'addons'])->name('addons');

Route::get('add-pelanggan', [PagesController::class, 'insertPeoples'])->name('customer.create');
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

Route::get('pelanggan1', function () {
    return view('admin.person');
})->name('customer.create');
Route::get('pelanggan2', function () {
    return view('admin.person');
})->name('customer.download');

Route::get('transaksi', [PagesController::class, 'transaction'])->name('transaction.index');
Route::get('transaksi1', function () {
    return view('admin.person');
})->name('transaction.success');
Route::get('transaksi2', function () {
    return view('admin.person');
})->name('transaction.pending');
Route::get('transaksi3', function () {
    return view('admin.person');
})->name('transaction.filter');

Route::prefix('user')->group(function () {
    Route::get('/request', function () {
        return view('admin.person');
    })->name('user.request');
    Route::get('/management', function () {
        return view('admin.person');
    })->name('user.management');
    Route::get('/officer', function () {
        return view('admin.person');
    })->name('user.officer');
    Route::get('/customer', function () {
        return view('admin.person');
    })->name('user.customer');
    Route::get('/role', function () {
        return view('admin.person');
    })->name('user.role');
});

Route::get('auth-login', function () {
    return view('auth.login');
})->name('auth-login');

Route::prefix('user')->group(function () {
    Route::get('/add-user', [PagesController::class, 'userForm'])->name('user.create');
    Route::get('/list', [PagesController::class, 'userList'])->name('user.list');
});

