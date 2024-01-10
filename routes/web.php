<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ImportDBController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LivestockController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\ArticleCategoryController;
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
Route::get('livestock-code', [ImportDBController::class, 'generateTernakCode']);
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
    Route::get('/report/export', [DashboardController::class, 'export'])->name('report.export');

    Route::group(["prefix" => '/farmers'], function () {
        Route::get('/', [FarmerController::class, 'index'])->name('farmer.index');
        Route::post('store', [FarmerController::class, 'store'])->name('farmer.store');
        Route::post('update', [FarmerController::class, 'update'])->name('farmer.update');
        Route::post('delete', [FarmerController::class, 'delete'])->name('farmer.delete');
    });

    Route::group(["prefix" => '/sliders'], function () {
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', [ArticleCategoryController::class, 'index'])->name('slider.category.index');
            Route::post('/store', [ArticleCategoryController::class, 'store'])->name('slider.category.store');
            Route::post('/{id}/update', [ArticleCategoryController::class, 'update'])->name('slider.category.update');
            Route::post('/{id}/delete', [ArticleCategoryController::class, 'delete'])->name('slider.category.delete');
        });


        Route::group(['prefix' => '{slug}'], function () {
            Route::get('/', [ArticleController::class, 'index'])->name('slider.index');

            Route::post('store', [ArticleController::class, 'store'])->name('slider.store');
            Route::post('update', [ArticleController::class, 'update'])->name('slider.update');
            Route::post('delete', [ArticleController::class, 'delete'])->name('slider.delete');
        });
    });

    Route::group(["prefix" => '/livestocks'], function () {
        Route::get('/report', [LivestockController::class, 'report'])->name('livestock.report');
        Route::get('/report/detail/{urlType}/{livetockTypeId}', [LivestockController::class, 'reportDetail'])->name('livestock.report.detail');
        Route::get('/report/detail/{urlType}/{livetockTypeId}/export', [LivestockController::class, 'reportDetailExport'])->name('livestock.report.detail.export');
        Route::get('/', [LivestockController::class, 'index'])->name('livestock.index');
        Route::post('/update', [LivestockController::class, 'updateStatus'])->name('livestock.update-status');
    });

    Route::group(["prefix" => '/references', 'as' => 'reference.'], function () {
        Route::get('/regencies', [ReferenceController::class, 'regencyList'])->name('regency');
        Route::get('/districts', [ReferenceController::class, 'districtList'])->name('district');
        Route::get('/villages', [ReferenceController::class, 'villageList'])->name('village');
    });

    Route::post('logout', [AuthenticatedSessionControler::class, 'logout'])->name('logout');
});
