<?php

use App\Http\Controllers\admin\DeresanAController;
use App\Http\Controllers\admin\MurojaahController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ImportExcelController;

use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\SantriController;

use App\Http\Controllers\admin\TahsinBinnadhorController;

use App\Http\Controllers\admin\UstadTahfidzController;
use App\Http\Controllers\admin\ZiyadahController;
use App\Http\Controllers\admin\EvaluasiController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login-proses', [AuthController::class, 'loginProses'])->name('login-proses');

Route::get('/import', [ImportExcelController::class, 'index'])->name('index-import');
Route::post('/import', [ImportExcelController::class, 'updateSantri'])->name('import');

Route::get('/export-test', [DashboardController::class, 'exportBlangko'])->name('export-blangko');

Route::middleware(['login'])->group(function () {

    Route::group([
        'prefix' => '/dashboard',
        'as' => 'dashboard.',
    ], function () {
        Route::get('/', [DashboardController::class, 'index']);

        Route::post('/form-blangko', [DashboardController::class, 'blangko'])->name('form-blangko');
        Route::post('/diagram-ziyadah', [DashboardController::class, 'diagramZiyadah'])->name('diagram-ziyadah');
    });

    Route::group([
        'prefix' => '/user',
        'as' => 'user.',
        'middleware' => 'roleAccess:1'
    ], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/get-data', [UserController::class, 'getData'])->name('get-data');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::post('/store-password', [UserController::class, 'storePassword'])->name('password');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete');
    });

    Route::group([
        'prefix' => '/santri',
        'as' => 'santri.',
        'middleware' => 'roleAccess:1'
    ], function () {
        Route::get('/', [SantriController::class, 'index'])->name('index');
        Route::get('/get-data', [SantriController::class, 'getData'])->name('get-data');
        Route::get('/edit/{id}', [SantriController::class, 'edit'])->name('edit');
        Route::post('/store', [SantriController::class, 'store'])->name('store');
        Route::put('/update/{id}', [SantriController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SantriController::class, 'delete'])->name('delete');
    });

    Route::group([
        'prefix' => '/ketahfidzan',
        'as' => 'ketahfidzan.',
        'middleware' => 'roleAccess:1|2'
    ], function () {
        
        Route::group([
            'prefix' => '/ustad-tahfidz',
            'as' => 'ustad-tahfidz.',
            'middleware' => 'roleAccess:1'
        ], function () {
            Route::get('/', [UstadTahfidzController::class, 'index'])->name('index');
            Route::get('/get-data', [UstadTahfidzController::class, 'getData'])->name('get-data');

            Route::get('/detail/{id}', [UstadTahfidzController::class, 'detail'])->name('detail');
            Route::post('/store', [UstadTahfidzController::class, 'store'])->name('store');
            Route::post('/store-santri-ketahfidzan', [UstadTahfidzController::class, 'storeKetahfidzan'])->name('store-santri-ketahfidzan');

            Route::delete('/delete/{id}', [UstadTahfidzController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => '/tahfidzan-admin',
            'as' => 'tahfidzan-admin.',
            'middleware' => 'roleAccess:1|2'
        ], function () {
            
            Route::group([
                'prefix' => '/deresan-a',
                'as' => 'deresan-a.',
            ], function () {
                Route::get('/', [DeresanAController::class, 'index'])->name('index');
                Route::put('/update-value/{id}', [DeresanAController::class, 'updateValue'])->name('update-value');
            });

            Route::group([
                'prefix' => '/murojaah',
                'as' => 'murojaah.',
            ], function () {
                Route::get('/', [MurojaahController::class, 'index'])->name('index');
                Route::put('/update-value/{id}', [MurojaahController::class, 'updateValue'])->name('update-value');
            });

            Route::group([
                'prefix' => '/tahsin-binnadhor',
                'as' => 'tahsin-binnadhor.',
            ], function () {
                Route::get('/', [TahsinBinnadhorController::class, 'index'])->name('index');
                Route::put('/update-value/{id}', [TahsinBinnadhorController::class, 'updateValue'])->name('update-value');
            });

            Route::group([
                'prefix' => '/ziyadah',
                'as' => 'ziyadah.',
            ], function () {
                Route::get('/', [ZiyadahController::class, 'index'])->name('index');
                Route::put('/update-value/{id}', [ZiyadahController::class, 'updateValue'])->name('update-value');
            });
        });
        
        Route::group([
            'prefix' => '/evaluasi',
            'as' => 'evaluasi.',
            'middleware' => 'roleAccess:1|2'
        ], function () {
            Route::get('/', [EvaluasiController::class, 'index'])->name('index');
            Route::post('/tahfidzan', [EvaluasiController::class, 'updateEvaluasi'])->name('update-evaluasi');
        });
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

});