<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\LivestockController;
use App\Http\Controllers\Api\KandangController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'update']);

    Route::group(["prefix" => '/search'], function () {
        Route::get('/', [SearchController::class, 'index']);
    });

    Route::group(["prefix" => '/report'], function () {
        Route::get('/by-kandang-type', [ReportController::class, 'reportByKandangType']);
        Route::get('/by-kandang-id', [ReportController::class, 'reportByKandangId']);
        Route::get('/by-livestock-type', [ReportController::class, 'reportByLivestockType']);
    });

    Route::group(["prefix" => '/kandang'], function () {
        Route::get('/', [KandangController::class, 'index']);
        Route::post('/store', [KandangController::class, 'store']);
    });

    Route::group(["prefix" => '/livestocks'], function () {
        Route::get('/', [LivestockController::class, 'index']);
        Route::post('/store', [LivestockController::class, 'store']);
        Route::post('/births', [LivestockController::class, 'birthStore']);
        Route::post('/deads', [LivestockController::class, 'deadUpdate']);
        Route::post('/status/update', [LivestockController::class, 'updateStatus']);
    });

    Route::group(["prefix" => '/transactions'], function () {
        Route::get('/events', [TransactionController::class, 'indexEvent']);
        Route::post('/sells', [TransactionController::class, 'sell']);
        Route::post('/buys', [TransactionController::class, 'buy']);
    });

    Route::group(["prefix" => '/sliders'], function () {
        Route::get('/{type}', [SliderController::class, 'index']);
        Route::get('/{type}/{id}', [SliderController::class, 'show']);
    });

    Route::group(["prefix" => '/references'], function () {
        Route::get('/provinces', [ReferenceController::class, 'provinceList']);
        Route::get('/regencies', [ReferenceController::class, 'regencyList']);
        Route::get('/districts', [ReferenceController::class, 'districtList']);
        Route::get('/villages', [ReferenceController::class, 'villageList']);

        Route::get('/pakan', [ReferenceController::class, 'pakanList']);
        Route::get('/limbah', [ReferenceController::class, 'limbahList']);
        Route::get('/livestock-types', [ReferenceController::class, 'livestockTypeList']);
    }); 
});
