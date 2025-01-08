<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\TahfidzanController;

Route::group([
    'prefix' => '/tahfidzan',
    'as' => 'tahfidzan.'
], function () {
    Route::get('/', [TahfidzanController::class, 'index'])->name('index');
    Route::put('/update-value/{id}', [TahfidzanController::class, 'updateValue'])->name('update-value');
});