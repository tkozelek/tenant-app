<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\GlobalProduct;
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
        $products = GlobalProduct::where('is_active', true)->with(['category', 'media'])->latest()->paginate(12);

        return view('products.index', compact('categories', 'products'));
    }

    public function show(Category $category): View
    {
        // nacitam rodicovsku kategoriu a deti
        $category->load('parent', 'children');

        $products = $category->allGlobalProducts()
            ->where('is_active', true)
            ->with(['category', 'media', 'globalProductAttributes.attribute', 'globalProductAttributes.attributeValue'])
            ->latest()
            ->paginate(12);

        $attributes = $category->allAttributes()// collection
            ->where('is_filterable', true);

        $attributeRanges = [];
        foreach ($attributes->where('type', 'number') as $attr) { // filter nad collection/ bez db
            $range = DB::table('global_product_attributes')
                ->where('attribute_id', $attr->id)
                ->whereNotNull('custom_value')
                ->selectRaw('MIN(CAST(custom_value AS DECIMAL(10,2))) as min_val, MAX(CAST(custom_value AS DECIMAL(10,2))) as max_val')
                ->first();

            $attributeRanges[$attr->id] = [
                'min' => (float) ($range->min_val ?? 0),
                'max' => (float) ($range->max_val ?? 100),
            ];
        }

        return view('products.show', compact('category', 'products', 'attributes', 'attributeRanges'));
    }

    public function product(GlobalProduct $globalProduct): View
    {
        $globalProduct->load([
            'category',
            'media',
            'globalProductAttributes.attribute',
            'globalProductAttributes.attributeValue',
            'tenantProducts.tenant',
            'tenantProducts.variants.activePriceHistory',
            'tenantProducts.variants.variantAttributes',
        ]);

        return view('products.product', compact('globalProduct'));
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
