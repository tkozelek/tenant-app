<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductVariantResource;
use App\Models\PriceHistory;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportController extends Controller
{
    public function stockHealth(Request $request, Tenant $tenant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $variants = TenantProductVariant::query()
            ->whereHas('product', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with('product')
            ->orderBy('stock_quantity')
            ->get();

        return ProductVariantResource::collection($variants);
    }

    public function expiringPrices(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $expiring = PriceHistory::query()
            ->whereHas('variant.product', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->where('valid_from', '<=', now())
            ->whereNotNull('valid_to')
            ->where('valid_to', '>=', now())
            ->where('valid_to', '<=', now()->addDays(7))
            ->with('variant.product')
            ->orderBy('valid_to')
            ->get();

        return response()->json([
            'data' => $expiring->map(fn (PriceHistory $ph) => [
                'product' => $ph->variant->product->name,
                'variant' => $ph->variant->name,
                'sku' => $ph->variant->sku,
                'price' => $ph->price,
                'valid_to' => $ph->valid_to,
                'days_left' => (int) now()->diffInDays($ph->valid_to),
            ]),
        ]);
    }
}
