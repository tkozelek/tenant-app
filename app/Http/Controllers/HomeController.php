<?php

namespace App\Http\Controllers;

use App\Models\GlobalProduct;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = GlobalProduct::featured()
            ->with([
                'category',
                'media',
                'tenantProducts.variants.activePriceHistory',
                'tenantProducts.variants.activeQuantityPrices',
            ])
            ->latest('updated_at')
            ->limit(6)
            ->get();

        $featuredPrices = $featuredProducts->mapWithKeys(function (GlobalProduct $product) {
            $lowest = $product->tenantProducts
                ->flatMap(fn ($tp) => $tp->variants)
                ->map(function ($variant) {
                    $prices = collect([$variant->current_price]);
                    if ($variant->relationLoaded('activeQuantityPrices')) {
                        $prices = $prices->merge($variant->activeQuantityPrices->pluck('price'));
                    }

                    return $prices->filter()->min();
                })
                ->filter()
                ->min();

            return [$product->id => $lowest];
        });

        return view('welcome', compact('featuredProducts', 'featuredPrices'));
    }
}
