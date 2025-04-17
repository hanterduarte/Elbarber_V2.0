<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashRegisterController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::prefix('cash-register')->group(function () {
    Route::get('/status', [CashRegisterController::class, 'status']);
    Route::post('/open', [CashRegisterController::class, 'open']);
    Route::post('/close', [CashRegisterController::class, 'close']);
    Route::post('/withdrawal', [CashRegisterController::class, 'withdrawal']);
}); 