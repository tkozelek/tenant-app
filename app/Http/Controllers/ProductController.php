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
            'tenantProducts.tenant.media',
            'tenantProducts.variants.activePriceHistory',
            'tenantProducts.variants.variantAttributes',
        ]);

        $groupedAttributes = $globalProduct->globalProductAttributes
            ->groupBy(fn ($row) => $row->attribute?->name);

        $variantIds = $globalProduct->variants()->pluck('tenant_product_variants.id');

        $priceHistory = PriceHistory::query()
            ->whereIn('tenant_product_variant_id', $variantIds)
            ->select(
                DB::raw('YEARWEEK(valid_from, 1) as week_key'),
                DB::raw("DATE_FORMAT(MIN(valid_from), '%d.%m.%Y') as week_label"),
                DB::raw('ROUND(AVG(price), 2) as avg_price'),
                DB::raw('ROUND(MIN(price), 2) as min_price'),
            )
            ->groupBy('week_key')
            ->orderBy('week_key')
            ->get();

        return view('products.product', compact('globalProduct', 'groupedAttributes', 'priceHistory'));
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
