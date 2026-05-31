<?php

use Illuminate\Support\Facades\Route;

// Login Controller
use App\Http\Controllers\LoginController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

// Dashboard Controller
use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Item Controller
use App\Http\Controllers\ItemController;
Route::get('/stok-bahan', [ItemController::class, 'index'])->name('items.index');
Route::post('/stok-bahan', [ItemController::class, 'store'])->name('items.store');
Route::put('/stok-bahan/{id}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/stok-bahan/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
Route::put('/items/restock/{id}', [ItemController::class, 'restock'])->name('items.restock');

// User Manage Controller
use App\Http\Controllers\UserController;
Route::get('/manajemen-akun', [UserController::class, 'index'])->name('users.index');
Route::post('/manajemen-akun', [UserController::class, 'store'])->name('users.store');
Route::put('/manajemen-akun/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/manajemen-akun/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Supplier Controller
use App\Http\Controllers\SupplierController;
Route::resource('suppliers', SupplierController::class);

// Order Controller
use App\Http\Controllers\OrderController;
Route::resource('orders', OrderController::class);
Route::get('orders/{id}/check-materials', [OrderController::class, 'checkMaterials'])->name('orders.check'); //Route cek kebutuhan bahan

Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/{id}/start-production/{deduct}', [OrderController::class, 'startProduction'])->name('orders.startProduction');

Route::post('/orders/finish-production/{id_production}', [OrderController::class, 'finishProduction'])
    ->name('orders.finishProduction');

Route::get('/orders/{id}/check-materials', [OrderController::class, 'showCalculation'])->name('orders.check');


// Production Controller
Route::get('/production', [OrderController::class, 'productionIndex'])->name('production.index');
Route::post('/production/{id}/update-stage', [OrderController::class, 'updateStage'])->name('production.updateStage');
Route::post('/production/{id}/materials', [ProductionController::class, 'updateMaterialUsage'])
    ->name('production.materials.update');


// Product Controller
use App\Http\Controllers\ProductController;
Route::resource('products', ProductController::class);

// BOM Controller
use App\Http\Controllers\BomController;
Route::resource('bom', BomController::class);

// PURCHASE Controller
use App\Http\Controllers\PurchaseController;
Route::resource('purchases', PurchaseController::class);
Route::post('/purchases/{id}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
Route::get('/purchases/{id}', [PurchaseController::class,'show'])->name('purchases.show');
Route::post('/purchases/{id}/status', [PurchaseController::class,'updateStatus'])->name('purchases.updateStatus');
Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');


// Shipping
Route::post('/orders/{id}/process-shipping', [OrderController::class, 'processShipping'])->name('orders.shipping.process');
Route::post('/orders/{id}/delivered', [OrderController::class, 'markAsDelivered'])->name('orders.complete');

Route::get('/orders/shipping-reports', [OrderController::class, 'shippingReports'])->name('orders.shipping_reports');

// REPORT CONTROLLER
use App\Http\Controllers\ReportController;
Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
Route::get('/laporan/penjualan', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('/laporan/produksi', [ReportController::class, 'production'])->name('reports.production');
Route::get('/laporan/stok-bahan', [ReportController::class, 'material'])->name('reports.material');
Route::get('/laporan/mutasi-stok', [ReportController::class, 'mutation'])->name('reports.mutation');
Route::get('/reports/sales/excel', [ReportController::class, 'salesExcel'])->name('reports.sales.excel');
Route::get('/reports/production/excel', [ReportController::class, 'productionExcel'])->name('reports.production.excel');
Route::get('/reports/material/excel', [ReportController::class, 'materialExcel'])->name('reports.material.excel');
Route::get('/reports/mutation/excel', [ReportController::class, 'mutationExcel'])->name('reports.mutation.excel');
Route::get('/laporan/stok-bahan/pdf', [ReportController::class, 'materialPdf'])->name('reports.material.pdf');
Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
Route::get('/reports/production/pdf', [ReportController::class, 'productionPdf'])->name('reports.production.pdf');
Route::get('/reports/mutation/pdf', [ReportController::class, 'mutationPdf'])->name('reports.mutation.pdf');