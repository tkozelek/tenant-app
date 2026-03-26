<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request, Tenant $tenant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission: '.ApiPermission::ProductsRead->value
        );

        $products = TenantProduct::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['variants.quantityPrices', 'variants.activePriceHistory'])
            ->paginate(25);

        return ProductResource::collection($products);
    }

    public function show(Request $request, Tenant $tenant, TenantProduct $product): ProductResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission: '.ApiPermission::ProductsRead->value
        );

        abort_unless($product->tenant_id === $tenant->id, 404);

        $product->load(['variants.quantityPrices', 'variants.activePriceHistory']);

        return new ProductResource($product);
    }
}
