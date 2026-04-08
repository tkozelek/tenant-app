<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/produkty', [CategoryController::class, 'index'])->name('products.index');
Route::get('/baliky', [BundleController::class, 'index'])->name('bundles.index');
Route::get('/produkty/{category:slug}', [CategoryController::class, 'show'])->name('products.show');
Route::get('/produkt/{globalProduct:slug}', [ProductController::class, 'show'])->name('products.product');
Route::get('/hladat', [ProductController::class, 'search'])->name('products.search');
Route::get('/porovnat', [CompareController::class, 'show'])->name('price.compare');
Route::get('/porovnat/search', [CompareController::class, 'search'])->name('price.compare.search');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/obchod/{tenant:slug}/role', [TenantUserController::class, 'show'])->name('tenant-user.index');

    Route::post('/obchod/{tenant:slug}/pridat', [TenantUserController::class, 'store'])->name('tenant-user.store');
});

// tenant CRUD
Route::middleware(['auth'])->group(function () {
    Route::get('/obchod/vytvorit', [TenantController::class, 'create'])->name('tenant.create');
    Route::get('/obchod/{tenant:slug}/upravit', [TenantController::class, 'edit'])->name('tenant.edit');

    Route::post('/obchod/vytvorit', [TenantController::class, 'store'])->name('tenant.store');
    Route::patch('/obchod/{tenant:slug}/upravit', [TenantController::class, 'update'])->name('tenant.update');
    Route::patch('/obchod/{tenant:slug}/zmenit-majitela', [TenantController::class, 'changeOwner'])->name('tenant.change-owner');
    Route::delete('/obchod/{tenant}/vymazat/obrazok/{title:bool}', [TenantController::class, 'deleteImage'])->name('tenant.image');

    Route::delete('/obchod/{tenant:slug}/vymazat', [TenantController::class, 'destroy'])->name('tenant.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/nastavenia', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/nastavenia', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/nastavenia', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/obchod/{tenant:slug}', [TenantController::class, 'show'])->name('tenant.show');

// Route::can('platform.access')->prefix('/admin')->name('admin.')->group(function () {
//    Route::get('/', [AdminController::class, 'index'])->name('index');
// });

require __DIR__.'/auth.php';
