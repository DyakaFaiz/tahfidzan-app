<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login-proses', [AuthController::class, 'loginProses'])->name('login-proses');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/', [UserController::class, 'index'])->name('user');

Route::group([
    'prefix' => '/user',
    'as' => 'user.'
], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/get-data', [UserController::class, 'getData'])->name('get-data-user');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::post('/store-password', [UserController::class, 'storePassword'])->name('password');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');