<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReferenceController;

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

    Route::group(["prefix" => '/references'], function () {
        Route::get('/provinces', [ReferenceController::class, 'provinceList']);
        Route::get('/regencies', [ReferenceController::class, 'regencyList']);
        Route::get('/districts', [ReferenceController::class, 'districtList']);
        Route::get('/villages', [ReferenceController::class, 'villageList']);
    }); 
});
