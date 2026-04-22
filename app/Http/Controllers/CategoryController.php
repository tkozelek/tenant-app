<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $products = GlobalProduct::query()
            ->where('is_active', true)
            ->with(['category', 'media'])
            ->withCount('tenantProducts')
            ->latest()
            ->paginate(12);

        return view('products.index', compact('categories', 'products'));
    }

    public function show(Category $category): View
    {
        $category->load('parent', 'children');

        return view('products.show', compact('category'));
    }
}
