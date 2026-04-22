<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\TenantProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ProductsRead->value),
            403,
            'Missing permission: '.ApiPermission::ProductsRead->value
        );

        $q = $request->string('q')->trim();

        abort_if($q->isEmpty(), 422, 'Query parameter "q" is required.');

        $products = TenantProduct::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'slug')
            ->limit(10)
            ->get()
            ->map(fn ($p) => ['type' => 'product', 'id' => $p->id, 'name' => $p->name, 'slug' => $p->slug]);

        $variants = TenantProductVariant::query()
            ->whereHas('product', fn ($query) => $query->where('tenant_id', $tenant->id)->where('is_active', true))
            ->where(function ($query) use ($q): void {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('ean', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'sku', 'ean')
            ->limit(10)
            ->get()
            ->map(fn ($v) => ['type' => 'variant', 'id' => $v->id, 'name' => $v->name, 'sku' => $v->sku, 'ean' => $v->ean]);

        $bundles = Bundle::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'slug')
            ->limit(10)
            ->get()
            ->map(fn ($b) => ['type' => 'bundle', 'id' => $b->id, 'name' => $b->name, 'slug' => $b->slug]);

        return response()->json([
            'query' => (string) $q,
            'data' => $products->merge($variants)->merge($bundles)->values(),
        ]);
    }
}
