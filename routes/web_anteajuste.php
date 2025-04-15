<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CashRegisterController;

// Rotas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // Barbeiros
    Route::resource('barbers', BarberController::class);
    
    // Serviços
    Route::resource('services', ServiceController::class);
    
    // Produtos
    Route::resource('products', ProductController::class);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::post('/products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.add-stock');
    
    // Agendamentos
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Vendas
    Route::resource('sales', SaleController::class);
    Route::post('/sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    
    // Caixa
    Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register.index');
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
    Route::post('/cash-register/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::post('/cash-register/transaction', [CashRegisterController::class, 'transaction'])->name('cash-register.transaction');
    
    // Relatórios
    Route::get('/reports/services', [ReportController::class, 'services'])->name('reports.services');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
}); 