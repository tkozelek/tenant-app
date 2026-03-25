<?php

use App\Http\Controllers\Api\V1\BundleController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\VariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/{tenant:slug}')
    ->middleware('api.auth')
    ->group(function () {
        Route::apiResource('products', ProductController::class)->only(['index', 'show']);
        Route::apiResource('products.variants', VariantController::class)->only(['index', 'show']);

        Route::post('variants/{variant}/stock', [StockController::class, 'store']);

        Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
        Route::post('coupons/check', [CouponController::class, 'check']);
        Route::post('coupons/use', [CouponController::class, 'use']);
        Route::apiResource('bundles', BundleController::class)->only(['index', 'show']);

        Route::prefix('reports')->group(function () {
            Route::get('stock-health', [ReportController::class, 'stockHealth']);
            Route::get('expiring-prices', [ReportController::class, 'expiringPrices']);
        });
    });
