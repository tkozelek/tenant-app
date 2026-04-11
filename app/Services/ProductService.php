<?php

namespace App\Services;

use App\Models\GlobalProduct;
use App\Models\PriceHistory;
use Illuminate\Support\Collection;

class ProductService
{
    public function getProductPageData(GlobalProduct $globalProduct): array
    {
        $globalProduct->load([
            'category',
            'media',
            'globalProductAttributes.attribute',
            'globalProductAttributes.attributeValue',
            'tenantProducts' => fn ($q) => $q->where('is_active', true),
            'tenantProducts.tenant.media',
            'tenantProducts.variants.activePriceHistory',
            'tenantProducts.variants.activeQuantityPrices',
            'tenantProducts.variants.activeCoupons',
            'tenantProducts.variants.media',
            'category.activeCoupons',
            'variants.media',
            'variants.variantAttributes',
            'variants.bundles.tenant',
            'variants.bundles.media',
            'variants.bundles.items.variant.activePriceHistory',
            'variants.bundles.items.variant.media',
        ]);

        $groupedAttributes = $globalProduct->globalProductAttributes
            ->groupBy(fn ($row) => $row->attribute?->name);

        $bundles = $globalProduct->variants
            ->flatMap(fn ($v) => $v->bundles)
            ->where('is_active', true)
            ->unique('id')
            ->values();

        $lowestPricePerTenant = $this->computeLowestPricePerTenant($globalProduct->tenantProducts);

        $lowestPrice = $lowestPricePerTenant->filter()->min();

        $categoryCoupons = $globalProduct->category?->activeCoupons->filter() ?? collect();

        $priceHistory = $this->getPriceHistory($globalProduct);

        return compact(
            'globalProduct',
            'groupedAttributes',
            'bundles',
            'lowestPricePerTenant',
            'lowestPrice',
            'categoryCoupons',
            'priceHistory',
        );
    }

    private function computeLowestPricePerTenant(Collection $tenantProducts): Collection
    {
        return $tenantProducts->mapWithKeys(function ($tenantProduct) {
            $variantPrices = $tenantProduct->variants->map(function ($variant) {
                $prices = collect([$variant->current_price]);

                if ($variant->relationLoaded('activeQuantityPrices')) {
                    $prices = $prices->merge($variant->activeQuantityPrices->pluck('price'));
                }

                return $prices->filter()->min();
            });

            $lowest = $variantPrices->filter()->min();

            $tenantProduct->variants->each(function ($variant, $key) use ($variantPrices, $lowest) {
                $variant->is_cheapest = $lowest !== null
                    && isset($variantPrices[$key])
                    && bccomp((string) $variantPrices[$key], (string) $lowest, 2) === 0;
            });

            return [$tenantProduct->id => $lowest];
        });
    }

    private function getPriceHistory(GlobalProduct $globalProduct): Collection
    {
        $variantIds = $globalProduct->variants()->pluck('tenant_product_variants.id');

        return PriceHistory::query()
            ->whereIn('tenant_product_variant_id', $variantIds)
            ->selectRaw('
                YEARWEEK(valid_from, 1) as week_key,
                DATE_FORMAT(MIN(valid_from), "%d.%m.%Y") as week_label,
                ROUND(AVG(price), 2) as avg_price,
                ROUND(MIN(price), 2) as min_price
            ')
            ->groupBy('week_key')
            ->orderBy('week_key')
            ->get();
    }
}
