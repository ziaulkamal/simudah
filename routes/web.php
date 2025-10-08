<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('pelanggan', function () {
    return view('admin.person');
})->name('customer');

Route::get('transaksi', function () {
    return view('admin.person');
})->name('transaction');


Route::get('auth-login', function () {
    return view('auth.login');
})->name('auth-login');
