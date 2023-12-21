<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
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

Route::middleware('web.guest')->group(function () {
    Route::get('login', [AuthenticatedSessionControler::class, 'loginIndex'])->name('login');
    Route::post('login', [AuthenticatedSessionControler::class, 'loginStore'])->name('login.store');
});

Route::middleware('web.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/sliders/today', [ArticleController::class, 'indexToday'])->name('slider.today');
    Route::get('/sliders/finance', [ArticleController::class, 'indexFinance'])->name('slider.finance');

    Route::post('/sliders/store', [ArticleController::class, 'store'])->name('slider.store');
    Route::post('/sliders/update', [ArticleController::class, 'update'])->name('slider.update');
    Route::post('/sliders/delete', [ArticleController::class, 'delete'])->name('slider.delete');

    Route::post('logout', [AuthenticatedSessionControler::class, 'logout'])->name('logout');
});
