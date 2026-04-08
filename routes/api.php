<?php

use App\Http\Controllers\Api\V1\BundleController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\PriceHistoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\TenantProfileController;
use App\Http\Controllers\Api\V1\VariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/{tenant:slug}')
    ->middleware('api.auth')
    ->name('api.v1.')
    ->group(function () {
        Route::get('/', [TenantProfileController::class, 'show']);

        Route::apiResource('products', ProductController::class)->only(['index', 'show']);
        Route::apiResource('products.variants', VariantController::class)->only(['index', 'show']);
        Route::get('variants/{sku}', [VariantController::class, 'findBySku'])->name('variants.find-by-sku');
        Route::get('variants/{variant}/price-history', [PriceHistoryController::class, 'variant']);
        Route::get('variants/{variant}/quantity-prices', [VariantController::class, 'quantityPrices']);
        Route::get('variants/{variant}/calculate-price', [VariantController::class, 'calculatePrice']);
        Route::get('variants/{variant}/stock-history', [VariantController::class, 'stockHistory']);
        Route::post('variants/{variant}/stock', [StockController::class, 'store']);

        Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
        Route::post('coupons/check', [CouponController::class, 'check']);
        Route::post('coupons/use', [CouponController::class, 'use']);

        Route::apiResource('bundles', BundleController::class)->only(['index', 'show']);
        Route::get('bundles/{bundle}/price-history', [PriceHistoryController::class, 'bundle']);

        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{category:slug}/products', [CategoryController::class, 'products']);

        Route::get('search', [SearchController::class, 'index']);

        Route::prefix('reports')->group(function () {
            Route::get('stock-health', [ReportController::class, 'stockHealth']);
            Route::get('expiring-prices', [ReportController::class, 'expiringPrices']);
            Route::get('price-history', [ReportController::class, 'priceHistory']);
            Route::get('stock-movements', [ReportController::class, 'stockMovements']);
            Route::get('coupon-usage', [ReportController::class, 'couponUsage']);
            Route::get('bundle-price-history', [ReportController::class, 'bundlePriceHistory']);
        });
    });
