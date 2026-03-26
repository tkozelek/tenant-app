<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PriceHistoryResource;
use App\Models\Bundle;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PriceHistoryController extends Controller
{
    public function variant(Request $request, Tenant $tenant, TenantProductVariant $variant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::PricesRead->value),
            403,
            'Missing permission: '.ApiPermission::PricesRead->value
        );

        abort_unless($variant->product->tenant_id === $tenant->id, 404);

        $histories = $variant->priceHistories()->orderBy('valid_from', 'desc')->paginate(25);

        return PriceHistoryResource::collection($histories);
    }

    public function bundle(Request $request, Tenant $tenant, Bundle $bundle): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::PricesRead->value),
            403,
            'Missing permission: '.ApiPermission::PricesRead->value
        );

        abort_unless($bundle->tenant_id === $tenant->id, 404);

        $histories = $bundle->priceHistories()->orderBy('valid_from', 'desc')->paginate(25);

        return PriceHistoryResource::collection($histories);
    }
}
