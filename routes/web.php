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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MechanicDashboardController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\PartRequestController;
use App\Http\Controllers\PartReturnController;
use App\Http\Controllers\ReportController;
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
    Route::post('mechanics/demo', [MechanicController::class, 'demo'])->name('mechanics.demo');
    Route::resource('mechanics', MechanicController::class);
    Route::post('spareparts/demo', [SparepartController::class, 'demo'])->name('spareparts.demo');
    Route::resource('spareparts', SparepartController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
    Route::get('/service-orders/vehicles', [ServiceOrderController::class, 'getVehicles'])->name('service-orders.vehicles');
    Route::resource('service-orders', ServiceOrderController::class);
    Route::get('/invoices/order-detail/{order}', [InvoiceController::class, 'getOrderDetail'])->name('invoices.order-detail');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('users', UserController::class);
    Route::post('checklist-items/demo', [ChecklistItemController::class, 'demo'])->name('checklist-items.demo');
    Route::resource('checklist-items', ChecklistItemController::class);
    Route::get('part-requests', [PartRequestController::class, 'index'])->name('part-requests.index');
    Route::post('part-requests/{detail}/fulfill', [PartRequestController::class, 'fulfill'])->name('part-requests.fulfill');
    Route::get('part-returns', [PartReturnController::class, 'index'])->name('part-returns.index');
    Route::post('part-returns/{partReturn}/confirm', [PartReturnController::class, 'confirm'])->name('part-returns.confirm');
    Route::get('reports/movements', [ReportController::class, 'movements'])->name('reports.movements');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::prefix('mechanic')->name('mechanic.')->group(function () {
        Route::get('/dashboard', [MechanicDashboardController::class, 'index'])->name('dashboard');
        Route::get('/services', [MechanicDashboardController::class, 'myServices'])->name('services');
        Route::get('/services/{serviceOrder}', [MechanicDashboardController::class, 'detail'])->name('detail');
        Route::post('/services/{serviceOrder}/status', [MechanicDashboardController::class, 'updateStatus'])->name('update-status');
        Route::post('/services/{serviceOrder}/progress', [MechanicDashboardController::class, 'saveProgress'])->name('save-progress');
        Route::post('/services/{serviceOrder}/request-part', [MechanicDashboardController::class, 'requestPart'])->name('request-part');
        Route::post('/part-detail/{detail}/return', [MechanicDashboardController::class, 'returnPart'])->name('return-part');
        Route::get('/history', [MechanicDashboardController::class, 'history'])->name('history');
        Route::get('/profile', [MechanicDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [MechanicDashboardController::class, 'updateProfile'])->name('update-profile');
    });
});
