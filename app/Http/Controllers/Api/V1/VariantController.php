<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductVariantResource;
use App\Http\Resources\Api\V1\QuantityPriceResource;
use App\Http\Resources\Api\V1\StockHistoryResource;
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

    public function quantityPrices(Request $request, Tenant $tenant, TenantProductVariant $variant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::PricesRead->value),
            403,
            'Missing permission: '.ApiPermission::PricesRead->value
        );

        abort_unless($variant->product->tenant_id === $tenant->id, 404);

        return QuantityPriceResource::collection(
            $variant->quantityPrices()->paginate(25)
        );
    }

    public function calculatePrice(Request $request, Tenant $tenant, TenantProductVariant $variant): \Illuminate\Http\JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::PricesRead->value),
            403,
            'Missing permission: '.ApiPermission::PricesRead->value
        );

        abort_unless($variant->product->tenant_id === $tenant->id, 404);

        $quantity = (int) $request->query('quantity', 1);
        abort_if($quantity < 1, 422, 'Quantity must be at least 1.');

        $variant->load(['activePriceHistory', 'activeQuantityPrices']);

        $unitPrice = $variant->current_price;
        $quantityTier = null;

        if (! $variant->activePriceHistory?->is_flash_sale) {
            $quantityTier = $variant->activeQuantityPrices
                ->first(fn ($qp) => $quantity >= $qp->min_quantity && ($qp->max_quantity === null || $quantity <= $qp->max_quantity));

            if ($quantityTier !== null) {
                $unitPrice = (float) $quantityTier->price;
            }
        }

        $totalPrice = $unitPrice !== null ? round($unitPrice * $quantity, 2) : null;

        $couponResult = null;
        $finalPrice = $totalPrice;

        if ($couponCode = $request->query('coupon')) {
            $coupon = \App\Models\Coupon::query()
                ->where('tenant_id', $tenant->id)
                ->where('code', $couponCode)
                ->first();

            if (! $coupon) {
                $couponResult = ['valid' => false, 'message' => 'Coupon not found.'];
            } elseif (! $coupon->isValid()) {
                $couponResult = ['valid' => false, 'message' => 'Coupon is expired, inactive, or has reached its usage limit.'];
            } elseif (! $coupon->appliesToVariant($variant)) {
                $couponResult = ['valid' => false, 'message' => 'Coupon is not valid for this product variant.'];
            } elseif ($coupon->min_order_amount !== null && $totalPrice < $coupon->min_order_amount) {
                $couponResult = ['valid' => false, 'message' => 'Total price does not meet the minimum order amount.', 'min_order_amount' => (float) $coupon->min_order_amount];
            } else {
                $discount = $coupon->discount_type === 'percentage'
                    ? round($totalPrice * ($coupon->value / 100), 2)
                    : min((float) $coupon->value, $totalPrice);

                $finalPrice = round($totalPrice - $discount, 2);

                $couponResult = [
                    'valid' => true,
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => (float) $coupon->value,
                    'discount_amount' => $discount,
                ];
            }
        }

        return response()->json([
            'variant_id' => $variant->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'final_price' => $finalPrice,
            'quantity_tier_applied' => $quantityTier !== null,
            'coupon' => $couponResult,
        ]);
    }

    public function stockHistory(Request $request, Tenant $tenant, TenantProductVariant $variant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::StockRead->value),
            403,
            'Missing permission: '.ApiPermission::StockRead->value
        );

        abort_unless($variant->product->tenant_id === $tenant->id, 404);

        return StockHistoryResource::collection(
            $variant->stockHistories()->latest()->paginate(25)
        );
    }
}
