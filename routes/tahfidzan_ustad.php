<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TahfidzanController;
use App\Http\Controllers\ustad\TahfidzanController as UstadTahfidzanController;

Route::group([
    'prefix' => '/tahfidzan',
    'as' => 'tahfidzan.'
], function () {
    Route::get('/', [UstadTahfidzanController::class, 'index'])->name('index');
    Route::put('/update-value/{id}', [TahfidzanController::class, 'updateValue'])->name('update-value');
});