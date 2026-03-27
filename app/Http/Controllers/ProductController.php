<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\Http\Request;
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
        $products = GlobalProduct::where('is_active', true)->with('category')->latest()->paginate(12);

        return view('products.index', compact('categories', 'products'));
    }

    public function show(Category $category): View
    {
        // nacitam rodicovsku kategoriu a deti
        $category->load('parent', 'children');

        // nacitam aktivne produkty z tejto kategorie aj podkategorii
        $products = $category->allGlobalProducts()
            ->where('is_active', true)
            ->with(['category', 'media', 'globalProductAttributes.attribute', 'globalProductAttributes.attributeValue'])
            ->latest()
            ->paginate(12);

//        dump('PRODUCTS', $products->pluck('name', 'id'));

        // nacitam atributy ktore su oznacene ako filtrovatelne
        $attributes = $category->allAttributes()
            ->where('is_filterable', true);

        return view('products.show', compact('category', 'products', 'attributes'));
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
