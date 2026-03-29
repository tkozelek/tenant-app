<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductVariantResource;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VariantController extends Controller
{
    public function index(Request $request, Tenant $tenant, TenantProduct $product): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission'
        );

        abort_unless($product->tenant_id === $tenant->id, 404);

        return ProductVariantResource::collection($product->variants()->with(['activeQuantityPrices', 'activePriceHistory', 'variantAttributes'])->get());
    }

    public function show(Request $request, Tenant $tenant, TenantProduct $product, TenantProductVariant $variant): ProductVariantResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission'
        );

        abort_unless($product->tenant_id === $tenant->id, 404);
        abort_unless($variant->tenant_product_id === $product->id, 404);

        $variant->load(['activeQuantityPrices', 'activePriceHistory', 'variantAttributes']);

        return new ProductVariantResource($variant);
    }

    public function findBySku(Request $request, Tenant $tenant, string $sku): ProductVariantResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission'
        );

        $variant = TenantProductVariant::query()
            ->whereHas('product', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->where('sku', $sku)
            ->with(['activeQuantityPrices', 'activePriceHistory', 'variantAttributes'])
            ->firstOrFail();

        return new ProductVariantResource($variant);
    }
}
