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

Route::get('/metadata', function () {
    return view('admin.dashboard');
})->name('dashboard.dua');

Route::get('pelanggan', function () {
    return view('admin.person');
})->name('customer');

Route::get('add-pelanggan', [PagesController::class, 'insertPeoples'])->name('customer.create');
Route::get('pelanggan', [PagesController::class, 'peoples'])->name('customer.index');
Route::get('{hash}', [PagesController::class, 'viewsPeople'])->name('customer.view');

Route::get('pelanggan1', function () {
    return view('admin.person');
})->name('customer.create');
Route::get('pelanggan2', function () {
    return view('admin.person');
})->name('customer.download');

Route::get('transaksi', function () {
    return view('admin.person');
})->name('transaction');
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

