<?php

namespace App\Services;

use App\Builders\GenericFeedBuilder;
use App\Builders\HeurekaFeedBuilder;
use App\Enums\XmlFeedPortal;
use App\Models\TenantProductVariant;
use App\Models\XmlFeed;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

readonly class XmlFeedService
{

    public function __construct(
        private HeurekaFeedBuilder $heurekaFeedBuilder,
        private GenericFeedBuilder $genericFeedBuilder,
    )
    {}

    public function generate(XmlFeed $feed): string
    {
        $variants = $this->loadVariants($feed);

        return match ($feed->portal) {
            XmlFeedPortal::Heureka => $this->heurekaFeedBuilder->build($feed, $variants),
            XmlFeedPortal::Generic => $this->genericFeedBuilder->build($feed, $variants),
        };
    }

    private function loadVariants(XmlFeed $feed): LazyCollection
    {
        $pinnedVariantIds = $feed->variants()->pluck('tenant_product_variant_id');
        $pinnedCategoryIds = $feed->categories()->pluck('category_id');

        return TenantProductVariant::query()
            ->whereHas('product', fn ($q) => $q
                ->where('tenant_id', $feed->tenant_id)
                ->where('is_active', true)
            )
            ->when($pinnedVariantIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $pinnedVariantIds))
            ->when($pinnedVariantIds->isEmpty() && $pinnedCategoryIds->isNotEmpty(), fn ($q) => $q
                ->whereHas('product.globalProduct', fn ($gp) => $gp->whereIn('category_id', $pinnedCategoryIds))
            )
            ->when(! $feed->include_out_of_stock, fn ($q) => $q->where('stock_quantity', '>', 0))
            ->with([
                'product.globalProduct.category',
                'product.globalProduct.globalProductAttributes.attribute',
                'product.globalProduct.globalProductAttributes.attributeValue',
                'variantAttributes.attribute',
                'variantAttributes.attributeValue',
                'activePriceHistory',
            ])
            ->cursor();
    }
}
