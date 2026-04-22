<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BundleResource;
use App\Models\Bundle;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BundleController extends Controller
{
    public function index(Request $request, Tenant $tenant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::BundlesRead->value),
            403,
            'Missing permission: '.ApiPermission::BundlesRead->value
        );

        $bundles = Bundle::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with(['activePriceHistory', 'items.variant'])
            ->paginate(25);

        return BundleResource::collection($bundles);
    }

    public function show(Request $request, Tenant $tenant, Bundle $bundle): BundleResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::BundlesRead->value),
            403,
            'Missing permission: '.ApiPermission::BundlesRead->value
        );

        abort_unless($bundle->tenant_id === $tenant->id, 404);

        $bundle->load(['activePriceHistory', 'items.variant']);

        return new BundleResource($bundle);
    }
}
