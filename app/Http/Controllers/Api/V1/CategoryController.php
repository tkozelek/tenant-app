<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApiPermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Category;
use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CategoriesRead->value),
            403,
            'Missing permission: '.ApiPermission::CategoriesRead->value
        );

        $categories = Category::query()
            ->whereHas('globalProducts.tenantProducts', fn ($q) => $q->where('tenant_id', $tenant->id)->where('is_active', true))
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function products(Request $request, Tenant $tenant, Category $category): AnonymousResourceCollection
    {
        abort_unless(
            $request->attributes->get('api_token')->can(ApiPermission::CategoriesRead->value),
            403,
            'Missing permission: '.ApiPermission::CategoriesRead->value
        );

        $categoryIds = $category->subtreeCategoryIds();

        $products = TenantProduct::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereHas('globalProduct', fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->with(['variants.activeQuantityPrices', 'variants.activePriceHistory', 'variants.variantAttributes'])
            ->paginate(25);

        return ProductResource::collection($products);
    }
}
