<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        // nacitam iba top-level kategorie aj s ich podkategoriami
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        // nacitam najnovsie aktivne produkty
        $products = GlobalProduct::where('is_active', true)->with(['category', 'media'])->withCount('tenantProducts')->latest()->paginate(12);

        return view('products.index', compact('categories', 'products'));
    }

    public function show(Category $category): View
    {
        $category->load('parent', 'children');

        return view('products.show', compact('category'));
    }

    public function product(GlobalProduct $globalProduct): View
    {
        $globalProduct->load([
            'category',
            'media',
            'globalProductAttributes.attribute',
            'globalProductAttributes.attributeValue',
            'tenantProducts' => fn ($q) => $q->where('is_active', true),
            'tenantProducts.tenant.media',
            'tenantProducts.variants.activePriceHistory',
            'tenantProducts.variants.activeQuantityPrices',
            'variants.media',
            'variants.variantAttributes',
            'variants.bundles.tenant',
            'variants.bundles.media',
            'variants.bundles.items.variant.activePriceHistory',
            'variants.bundles.items.variant.media',
        ]);

        $groupedAttributes = $globalProduct->globalProductAttributes
            ->groupBy(fn ($row) => $row->attribute?->name);

        $bundles = $globalProduct->variants
            ->flatMap(fn ($v) => $v->bundles)
            ->where('is_active', true)
            ->unique('id')
            ->values();

        $lowestPricePerTenant = $globalProduct->tenantProducts
            ->mapWithKeys(function ($tenantProduct) {
                $lowest = $tenantProduct->variants
                    ->map(function ($v) {
                        $prices = collect([$v->current_price]);
                        if ($v->relationLoaded('activeQuantityPrices')) {
                            $prices = $prices->merge($v->activeQuantityPrices->pluck('price'));
                        }

                        return $prices->filter()->min();
                    })
                    ->filter()
                    ->min();

                return [$tenantProduct->id => $lowest];
            });

        $lowestPrice = $lowestPricePerTenant->filter()->min();

        $variantIds = $globalProduct->variants()->pluck('tenant_product_variants.id');

        $priceHistory = PriceHistory::query()
            ->whereIn('tenant_product_variant_id', $variantIds)
            ->selectRaw('
                YEARWEEK(valid_from, 1) as week_key,
                DATE_FORMAT(MIN(valid_from), "%d.%m.%Y") as week_label,
                ROUND(AVG(price), 2) as avg_price,
                ROUND(MIN(price), 2) as min_price
            ')
            ->groupBy('week_key')
            ->orderBy('week_key')
            ->get();

        return view('products.product', compact('globalProduct', 'groupedAttributes', 'priceHistory', 'bundles', 'lowestPrice', 'lowestPricePerTenant'));
    }

    public function search(Request $request): View
    {
        // q v url je search
        $query = $request->get('q', '');

        $categories = Category::query()
            ->where('name', 'like', "%{$query}%")
            ->get();

        $products = GlobalProduct::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->with(['category', 'media'])
            ->paginate(12);

        return view('products.search', compact('products', 'categories', 'query'));
    }
}
