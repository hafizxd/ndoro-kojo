<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('login', [AuthController::class, 'loginIndex'])->name('loginIndex');

Route::middleware('web.guest')->group(function () {
    Route::get('login', [AuthenticatedSessionControler::class, 'loginIndex'])->name('login');
    Route::post('login', [AuthenticatedSessionControler::class, 'loginStore']);
});

Route::middleware('web.auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionControler::class, 'destroy'])->name('logout');
});

// require __DIR__.'/auth.php';
