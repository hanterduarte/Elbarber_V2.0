<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CashRegisterMovementController;
use App\Http\Controllers\AppointmentController;

// Rotas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Usuários
    Route::resource('users', UserController::class)->middleware('check-permission:manage_users');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('check-permission:manage_users')->name('users.toggle-status');

    // Roles
    Route::resource('roles', RoleController::class)->middleware('check-permission:manage_roles');
    Route::post('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->middleware('check-permission:manage_roles')->name('roles.toggle-status');

    // Permissões
    Route::resource('permissions', PermissionController::class)->middleware('check-permission:manage_permissions');
    Route::post('permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->middleware('check-permission:manage_permissions')->name('permissions.toggle-status');

    // Clientes
    Route::resource('clients', ClientController::class);
    
    // Barbeiros
    Route::resource('barbers', BarberController::class);
    
    // Serviços
    Route::resource('services', ServiceController::class);
    
    // Produtos
    Route::resource('products', ProductController::class);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::post('/products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.add-stock');
    
    // Vendas
    Route::resource('sales', SaleController::class);
    Route::post('/sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::post('/sales/{sale}/items', [SaleController::class, 'addItem'])->name('sales.add-item');
    Route::delete('/sales/{sale}/items/{item}', [SaleController::class, 'removeItem'])->name('sales.remove-item');
    
    // Métodos de Pagamento
    Route::resource('payment-methods', PaymentMethodController::class);
    
    // Rotas do Caixa
    Route::prefix('cash-register')->name('cash-register.')->group(function () {
        Route::get('/', [CashRegisterController::class, 'index'])->name('index');
        Route::post('/open', [CashRegisterController::class, 'open'])->name('open');
        Route::post('/close', [CashRegisterController::class, 'close'])->name('close');
        
        Route::prefix('movement')->name('movement.')->group(function () {
            Route::get('/create', [CashRegisterMovementController::class, 'create'])->name('create');
            Route::post('/store', [CashRegisterMovementController::class, 'store'])->name('store');
        });
    });
    
    // Relatórios
    Route::get('/reports/services', [ReportController::class, 'services'])->name('reports.services');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/cash-register', [ReportController::class, 'cashRegister'])->name('reports.cash-register');

    // Perfis
    Route::resource('profiles', ProfileController::class);

    // PDV
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/sale', [PosController::class, 'store'])->name('pos.store');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
}); 