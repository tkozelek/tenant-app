<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function show(GlobalProduct $globalProduct): View
    {
        $data = $this->productService->getProductPageData($globalProduct);

        return view('products.product', $data);
    }

    public function search(Request $request): View
    {
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
