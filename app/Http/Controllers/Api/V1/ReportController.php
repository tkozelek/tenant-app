<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductVariantResource;
use App\Models\BundlePriceHistory;
use App\Models\Coupon;
use App\Models\PriceHistory;
use App\Models\StockHistory;
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
            ->with(['product', 'activePriceHistory'])
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

    public function priceHistory(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $history = PriceHistory::query()
            ->whereHas('variant.product', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->where('valid_from', '>=', $from)
            ->where('valid_from', '<=', $to)
            ->with('variant.product')
            ->orderBy('valid_from', 'desc')
            ->get();

        return response()->json([
            'data' => $history->map(fn (PriceHistory $ph) => [
                'product' => $ph->variant->product->name,
                'variant' => $ph->variant->name,
                'sku' => $ph->variant->sku,
                'price' => (float) $ph->price,
                'original_price' => $ph->original_price !== null ? (float) $ph->original_price : null,
                'valid_from' => $ph->valid_from,
                'valid_to' => $ph->valid_to,
            ]),
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'total' => $history->count(),
            ],
        ]);
    }

    public function stockMovements(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $movements = StockHistory::query()
            ->whereHas('variant.product', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('type, SUM(quantity) as total_quantity, COUNT(*) as total_movements')
            ->groupBy('type')
            ->get();

        return response()->json([
            'data' => $movements,
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
        ]);
    }

    public function couponUsage(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $coupons = Coupon::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('used_count', 'desc')
            ->get();

        return response()->json([
            'data' => $coupons->map(fn (Coupon $c) => [
                'code' => $c->code,
                'description' => $c->description,
                'discount_type' => $c->discount_type,
                'value' => (float) $c->value,
                'used_count' => $c->used_count,
                'usage_limit' => $c->usage_limit,
                'usage_remaining' => $c->usage_limit !== null ? max(0, $c->usage_limit - $c->used_count) : null,
                'is_active' => (bool) $c->is_active,
                'starts_at' => $c->starts_at,
                'expires_at' => $c->expires_at,
            ]),
        ]);
    }

    public function bundlePriceHistory(Request $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::ReportsRead->value),
            403,
            'Missing permission: '.ApiPermission::ReportsRead->value
        );

        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $history = BundlePriceHistory::query()
            ->whereHas('bundle', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->where('valid_from', '>=', $from)
            ->where('valid_from', '<=', $to)
            ->with('bundle')
            ->orderBy('valid_from', 'desc')
            ->get();

        return response()->json([
            'data' => $history->map(fn (BundlePriceHistory $bph) => [
                'bundle' => $bph->bundle->name,
                'price' => (float) $bph->price,
                'original_price' => $bph->original_price !== null ? (float) $bph->original_price : null,
                'valid_from' => $bph->valid_from,
                'valid_to' => $bph->valid_to,
            ]),
            'meta' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'total' => $history->count(),
            ],
        ]);
    }
}
