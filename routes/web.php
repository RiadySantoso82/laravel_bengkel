<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SparepartCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\MasterSeedController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\SparepartController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/master/seed/{table}', [MasterSeedController::class, 'seed'])->name('master.seed');

    Route::resource('sparepart-categories', SparepartCategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::post('customers/demo', [CustomerController::class, 'demo'])->name('customers.demo');
    Route::resource('customers', CustomerController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::post('suppliers/demo', [SupplierController::class, 'demo'])->name('suppliers.demo');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('mechanics', MechanicController::class);
    Route::resource('spareparts', SparepartController::class);
});
