<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantProfileController extends Controller
{
    public function show(Request $request, Tenant $tenant): TenantResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission: '.ApiPermission::ProductsRead->value
        );

        return new TenantResource($tenant);
    }
}
