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
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CashRegisterMovementController;

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
    Route::resource('users', UserController::class);
    Route::get('users/{user}/roles', [UserController::class, 'roles'])->name('users.roles');
    Route::post('users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.assign-roles');

    // Roles
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');

    // Permissões
    Route::resource('permissions', PermissionController::class);

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
    
    // Agendamentos
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
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
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/cash-register', [ReportController::class, 'cashRegister'])->name('reports.cash-register');

    // Perfis
    Route::resource('profiles', ProfileController::class);

    // PDV
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/sale', [PosController::class, 'store'])->name('pos.store');
});

// Rotas de Permissões
Route::middleware(['auth'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:manage settings')->name('permissions.index');
    Route::put('/permissions/{role}', [PermissionController::class, 'updateRolePermissions'])->middleware('permission:manage settings')->name('permissions.update');
    Route::post('/permissions/sync', [PermissionController::class, 'syncDefaultPermissions'])->middleware('permission:manage settings')->name('permissions.sync');
});

// Rotas de Roles
Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class)->middleware('permission:manage settings');
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->middleware('permission:manage settings')->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:manage settings')->name('roles.assign-permissions');
});

// Rotas de Usuários
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class)->middleware('permission:view users');
    Route::get('users/{user}/roles', [UserController::class, 'roles'])->middleware('permission:edit users')->name('users.roles');
    Route::post('users/{user}/roles', [UserController::class, 'assignRoles'])->middleware('permission:edit users')->name('users.assign-roles');
}); 