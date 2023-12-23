<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ImportDBController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthenticatedSessionControler;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('excel', [ImportDBController::class, 'excel']);
Route::get('excel-query', [ImportDBController::class, 'excelQuery']);
Route::get('livestock-type', [ImportDBController::class, 'livestockType']);
Route::get('province', [ImportDBController::class, 'province']);
Route::get('regency', [ImportDBController::class, 'regency']);
Route::get('district', [ImportDBController::class, 'district']);
Route::get('village', [ImportDBController::class, 'village']);

Route::middleware('web.guest')->group(function () {
    Route::get('login', [AuthenticatedSessionControler::class, 'loginIndex'])->name('login');
    Route::post('login', [AuthenticatedSessionControler::class, 'loginStore'])->name('login.store');
});

Route::middleware('web.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::group(["prefix" => '/farmers'], function () {
        Route::get('/', [FarmerController::class, 'index'])->name('farmer.index');
        Route::post('store', [FarmerController::class, 'store'])->name('farmer.store');
        Route::post('update', [FarmerController::class, 'update'])->name('farmer.update');
        Route::post('delete', [FarmerController::class, 'delete'])->name('farmer.delete');
    });

    Route::group(["prefix" => '/sliders'], function () {
        Route::get('today', [ArticleController::class, 'indexToday'])->name('slider.today');
        Route::get('finance', [ArticleController::class, 'indexFinance'])->name('slider.finance');

        Route::post('store', [ArticleController::class, 'store'])->name('slider.store');
        Route::post('update', [ArticleController::class, 'update'])->name('slider.update');
        Route::post('delete', [ArticleController::class, 'delete'])->name('slider.delete');
    });

    Route::post('logout', [AuthenticatedSessionControler::class, 'logout'])->name('logout');
});
