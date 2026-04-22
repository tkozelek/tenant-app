<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckCouponRequest;
use App\Http\Requests\Api\V1\UseCouponRequest;
use App\Http\Resources\Api\V1\CouponResource;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends Controller
{
    public function index(Request $request, Tenant $tenant): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CouponsRead->value),
            403,
            'Missing permission: '.ApiPermission::CouponsRead->value
        );

        $coupons = Coupon::query()
            ->where('tenant_id', $tenant->id)
            ->paginate(25);

        return CouponResource::collection($coupons);
    }

    public function show(Request $request, Tenant $tenant, Coupon $coupon): CouponResource
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CouponsRead->value),
            403,
            'Missing permission: '.ApiPermission::CouponsRead->value
        );

        abort_unless($coupon->tenant_id === $tenant->id, 404);

        $coupon->load(['productVariants', 'categories']);

        return new CouponResource($coupon);
    }

    public function check(CheckCouponRequest $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CouponsRead->value),
            403,
            'Missing permission: '.ApiPermission::CouponsRead->value
        );

        $coupon = Coupon::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', $request->validated('code'))
            ->first();

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => 'Coupon not found.'], 404);
        }

        if (! $coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon is expired, inactive, or has reached its usage limit.',
                'coupon' => new CouponResource($coupon),
            ]);
        }

        $orderAmount = $request->validated('order_amount');

        if ($orderAmount !== null && $coupon->min_order_amount !== null && $orderAmount < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'Order amount does not meet the minimum required amount.',
                'min_order_amount' => $coupon->min_order_amount,
                'coupon' => new CouponResource($coupon),
            ]);
        }

        if ($variantId = $request->validated('variant_id')) {
            $variant = TenantProductVariant::find($variantId);

            if (! $variant || ! $coupon->appliesToVariant($variant)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Coupon is not valid for the specified product variant.',
                    'coupon' => new CouponResource($coupon),
                ]);
            }
        }

        if ($categoryId = $request->validated('category_id')) {
            $category = Category::find($categoryId);

            if (! $category || ! $coupon->appliesToCategory($category)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Coupon is not valid for the specified category.',
                    'coupon' => new CouponResource($coupon),
                ]);
            }
        }

        return response()->json([
            'valid' => true,
            'coupon' => new CouponResource($coupon),
        ]);
    }

    public function use(UseCouponRequest $request, Tenant $tenant): JsonResponse
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CouponsWrite->value),
            403,
            'Missing permission: '.ApiPermission::CouponsWrite->value
        );

        $coupon = Coupon::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', $request->validated('code'))
            ->first();

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found.'], 404);
        }

        if (! $coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is expired, inactive, or has reached its usage limit.',
            ], 422);
        }

        $orderAmount = $request->validated('order_amount');

        if ($orderAmount !== null && $coupon->min_order_amount !== null && $orderAmount < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Order amount does not meet the minimum required amount.',
                'min_order_amount' => $coupon->min_order_amount,
            ], 422);
        }

        if ($variantId = $request->validated('variant_id')) {
            $variant = TenantProductVariant::find($variantId);

            if (! $variant || ! $coupon->appliesToVariant($variant)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon is not valid for the specified product variant.',
                ], 422);
            }
        }

        if ($categoryId = $request->validated('category_id')) {
            $category = Category::find($categoryId);

            if (! $category || ! $coupon->appliesToCategory($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon is not valid for the specified category.',
                ], 422);
            }
        }

        $coupon->increment('used_count');

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'coupon' => new CouponResource($coupon->fresh()),
        ]);
    }
}
