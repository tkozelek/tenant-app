<?php

namespace App\Http\Controllers;

use App\Models\GlobalProduct;
use App\Models\PriceHistory;
use App\Models\TenantProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompareController extends Controller
{
    private const int MAX_PRODUCTS = 4;

    private const array COLORS = ['#60a5fa', '#34d399', '#fb923c', '#c084fc'];

    public function show(Request $request): View
    {
        $mode = $request->input('mode', 'product');

        if ($mode === 'variant') {
            return $this->showVariants($request);
        }

        return $this->showProducts($request);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        $mode = $request->input('mode', 'product');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        if ($mode === 'variant') {
            $results = TenantProductVariant::query()
                ->with('product.tenant')
                ->where(function ($q) use ($query): void {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhere('ean', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(fn ($v) => [
                    'name' => $v->name.' - '.($v->product->tenant->name ?? ''),
                    'slug' => (string) $v->id,
                ]);

            return response()->json($results);
        }

        $results = GlobalProduct::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->limit(20)
            ->get(['name', 'slug'])
            ->map(fn ($p) => ['name' => $p->name, 'slug' => $p->slug]);

        return response()->json($results);
    }

    private function showProducts(Request $request): View
    {
        $slugs = array_slice(
            array_values(array_unique((array) $request->input('slugs', []))),
            0,
            self::MAX_PRODUCTS, // prvych 4 produktov
        );

        $products = GlobalProduct::query()
            ->whereIn('slug', $slugs)
            ->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->values()
            ->map(fn ($product, $index) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'color' => self::COLORS[$index],
            ]);

        return view('products.compare', [
            'products' => $products,
            'chartData' => $this->buildProductChartData($products),
            'slugs' => $slugs,
            'maxProducts' => self::MAX_PRODUCTS,
            'mode' => 'product',
        ]);
    }

    private function showVariants(Request $request): View
    {
        $ids = array_slice(
            array_values(array_unique((array) $request->input('ids', []))),
            0,
            self::MAX_PRODUCTS,
        );

        $variants = TenantProductVariant::query()
            ->whereIn('id', $ids)
            ->with('product.tenant')
            ->get()
            ->values()
            ->map(fn ($variant, $index) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'slug' => (string) $variant->id,
                'subtitle' => $variant->product->tenant->name ?? '',
                'color' => self::COLORS[$index],
            ]);

        return view('products.compare', [
            'products' => $variants,
            'chartData' => $this->buildVariantChartData($variants),
            'slugs' => $ids,
            'maxProducts' => self::MAX_PRODUCTS,
            'mode' => 'variant',
        ]);
    }

    private function buildProductChartData(Collection $products): array
    {
        if ($products->isEmpty()) {
            return ['labels' => [], 'datasets' => []];
        }

        $rows = PriceHistory::query()
            ->join('tenant_product_variants as tpv', 'tpv.id', '=', 'price_history.tenant_product_variant_id')
            ->join('tenant_products as tp', 'tp.id', '=', 'tpv.tenant_product_id')
            ->whereIn('tp.global_product_id', $products->pluck('id'))
            ->selectRaw('
                tp.global_product_id,
                YEARWEEK(price_history.valid_from, 1) as week_key,
                DATE_FORMAT(MIN(price_history.valid_from), "%d.%m.%Y") as week_label,
                ROUND(AVG(price_history.price), 2) as avg_price,
                ROUND(MIN(price_history.price), 2) as min_price,
                ROUND(MAX(price_history.price), 2) as max_price
            ')
            ->groupBy('tp.global_product_id', 'week_key')
            ->orderBy('week_key')
            ->get();

        $allWeeks = $rows->sortBy('week_key')->unique('week_key')->pluck('week_label', 'week_key')->all();
        $byProduct = $rows->groupBy('global_product_id');
        $datasets = [];

        //        dump($byProduct);
        //        dump($allWeeks);

        foreach ($products as $product) {
            $color = $product['color'];
            $productRows = $byProduct->get($product['id'])?->keyBy('week_key') ?? collect(); // pole produktov, ale podla roktyzden
            $avg = $min = $max = [];

            foreach (array_keys($allWeeks) as $weekKey) {
                $row = $productRows->get($weekKey);
                $avg[] = $row ? (float) $row->avg_price : null;
                $min[] = $row ? (float) $row->min_price : null;
                $max[] = $row ? (float) $row->max_price : null;
            }

            $base = ['borderColor' => $color, 'backgroundColor' => 'transparent', 'tension' => 0.3, 'spanGaps' => true];
            $name = $product['name'];

            $datasets[] = array_merge($base, ['label' => "{$name} avg", 'data' => $avg, 'borderWidth' => 2, 'borderDash' => [], 'pointRadius' => 3]);
            $datasets[] = array_merge($base, ['label' => "{$name} min", 'data' => $min, 'borderWidth' => 1.5, 'borderDash' => [6, 4], 'pointRadius' => 2]);
            $datasets[] = array_merge($base, ['label' => "{$name} max", 'data' => $max, 'borderWidth' => 1.5, 'borderDash' => [2, 3], 'pointRadius' => 2]);
        }

        return ['labels' => array_values($allWeeks), 'datasets' => $datasets];
    }

    private function buildVariantChartData(Collection $variants): array
    {
        if ($variants->isEmpty()) {
            return ['labels' => [], 'datasets' => []];
        }

        $rows = PriceHistory::query()
            ->whereIn('tenant_product_variant_id', $variants->pluck('id'))
            ->selectRaw('
                tenant_product_variant_id,
                YEARWEEK(valid_from, 1) as week_key,
                DATE_FORMAT(MIN(valid_from), "%d.%m.%Y") as week_label,
                ROUND(AVG(price), 2) as avg_price,
                ROUND(MIN(price), 2) as min_price,
                ROUND(MAX(price), 2) as max_price
            ')
            ->groupBy('tenant_product_variant_id', 'week_key')
            ->orderBy('week_key')
            ->get();

        $allWeeks = $rows->sortBy('week_key')->unique('week_key')->pluck('week_label', 'week_key')->all();
        $byVariant = $rows->groupBy('tenant_product_variant_id');
        $datasets = [];

        foreach ($variants as $variant) {
            $color = $variant['color'];
            $variantRows = $byVariant->get($variant['id'])?->keyBy('week_key') ?? collect();
            $avg = $min = $max = [];

            foreach (array_keys($allWeeks) as $weekKey) {
                $row = $variantRows->get($weekKey);
                $avg[] = $row ? (float) $row->avg_price : null;
                $min[] = $row ? (float) $row->min_price : null;
                $max[] = $row ? (float) $row->max_price : null;
            }

            $base = ['borderColor' => $color, 'backgroundColor' => 'transparent', 'tension' => 0.3, 'spanGaps' => true];
            $name = $variant['name'];

            $datasets[] = array_merge($base, ['label' => "{$name} avg", 'data' => $avg, 'borderWidth' => 2, 'borderDash' => [], 'pointRadius' => 3]);
            $datasets[] = array_merge($base, ['label' => "{$name} min", 'data' => $min, 'borderWidth' => 1.5, 'borderDash' => [6, 4], 'pointRadius' => 2]);
            $datasets[] = array_merge($base, ['label' => "{$name} max", 'data' => $max, 'borderWidth' => 1.5, 'borderDash' => [2, 3], 'pointRadius' => 2]);
        }

        return ['labels' => array_values($allWeeks), 'datasets' => $datasets];
    }
}
