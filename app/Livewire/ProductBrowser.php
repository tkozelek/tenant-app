<?php

namespace App\Livewire;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductAttribute;
use App\Models\PriceHistory;
use App\Models\Tenant;
use App\Models\TenantProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductBrowser extends Component
{
    use WithPagination;

    #[Locked]
    public int $categoryId;

    #[Url(as: 'obchody', except: [])]
    public array $selectedTenants = [];

    #[Url(as: 'hodnoty', except: [])]
    public array $selectedValues = [];

    #[Url(as: 'boolean', except: [])]
    public array $boolFilters = [];

    #[Url(as: 'rozsah', except: [])]
    public array $rangeFilters = [];

    public array $attributeRanges = [];

    public array $priceRange = [];

    #[Url(as: 'cena', except: [])]
    public array $priceFilter = [];

    #[Url(as: 'zoradit', except: 'date')]
    public string $sort = 'date';

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function mount(int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->initializeRangeFilters();
        $this->initializePriceFilter();
    }

    #[Computed]
    public function category(): Collection|\Illuminate\Database\Eloquent\Model
    {
        return Category::findOrFail($this->categoryId);
    }

    #[Computed]
    public function subCategoryIds(): array
    {
        return $this->category->subtreeCategoryIds();
    }

    #[Computed]
    public function tenants(): Collection
    {
        $categoryIds = $this->subCategoryIds();

        return Tenant::query()
            ->whereHas('products', fn ($q) => $q
                ->whereHas('globalProduct', fn ($gp) => $gp
                    ->whereIn('category_id', $categoryIds)
                    ->where('is_active', true)
                )
            )
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function filterAttributes(): Collection
    {
        $categoryIds = $this->subCategoryIds();

        return Attribute::query()
            ->where('is_filterable', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['attributeValues' => fn ($q) => $q->orderBy('sort_order')])
            ->get();
    }

    #[Computed]
    public function attributeValueCounts(): array
    {
        $filteredProductsQuery = GlobalProduct::query()->select('global_products.id');
        $this->applyFilters(
            query: $filteredProductsQuery
        );

        return GlobalProductAttribute::query()
            ->whereIn('global_product_id', $filteredProductsQuery)
            ->whereNotNull('attribute_value_id')
            ->selectRaw('attribute_value_id, COUNT(DISTINCT global_product_id) as products_count')
            ->groupBy('attribute_value_id')
            ->pluck('products_count', 'attribute_value_id')
            ->toArray();
    }

    #[Computed]
    public function attributeBoolCounts(): array
    {
        $filteredProductsQuery = GlobalProduct::query()->select('global_products.id');
        $this->applyFilters(
            query: $filteredProductsQuery
        );

        return GlobalProductAttribute::query()
            ->whereIn('global_product_id', $filteredProductsQuery)
            ->selectRaw('attribute_id, COUNT(DISTINCT global_product_id) as products_count')
            ->groupBy('attribute_id')
            ->pluck('products_count', 'attribute_id')
            ->toArray();
    }

    #[Computed]
    public function products(): LengthAwarePaginator
    {
        $query = GlobalProduct::query();
        $this->applyFilters($query);

        if ($this->sort === 'price') {
            $perVariantPrice = PriceHistory::query()
                ->active()
                ->select('price')
                ->whereColumn('tenant_product_variant_id', 'tenant_product_variants.id')
                ->orderByDesc('valid_from')
                ->orderByDesc('created_at')
                ->limit(1);

            // "select `price` from `price_history` where `valid_from` <= ? and (`valid_to` is null or `valid_to` >= ?) and `tenant_product_variant_id` = `tenant_product_variants`.`id` order by `valid_from` desc, `created_at` desc limit 1

            $minPriceSubquery = TenantProductVariant::query()
                ->whereHas('product', fn ($q) => $q
                    ->whereColumn('global_product_id', 'global_products.id')
                )
                ->selectRaw('MIN(('.$perVariantPrice->toSql().'))', $perVariantPrice->getBindings());

            $query->addSelect(['min_price' => $minPriceSubquery])
                ->orderByRaw('min_price IS NULL, min_price ASC');
        } else {
            $query->latest();
        }

        return $query
            ->with(['category', 'media', 'globalProductAttributes.attribute', 'globalProductAttributes.attributeValue', 'variants.activePriceHistory'])
            ->withCount('tenantProducts')
            ->paginate(12);
    }

    private function applyFilters(
        Builder $query
    ): Builder {
        $categoryIds = $this->subCategoryIds();

        $query->whereIn('category_id', $categoryIds)->where('is_active', true);

        if (! empty($this->selectedTenants)) {
            $query->whereHas('tenantProducts', fn ($q) => $q
                ->whereIn('tenant_id', $this->selectedTenants)
            );
        }

        if (! empty($this->selectedValues)) {
            // vyberieme napr. 128gb 256gb (10,12) group by velkost pamate id napr. 2
            $grouped = AttributeValue::whereIn('id', $this->selectedValues)
                ->get()
                ->groupBy('attribute_id');

            // 2 => [10,12]
            // 3 => 14,15

            foreach ($grouped as $attributeId => $values) {
                $query->whereHas('globalProductAttributes', fn ($q) => $q
                    ->where('attribute_id', $attributeId) // 2, 3
                    ->whereIn('attribute_value_id', $values->pluck('id')) // 10,12  ,  14,15 etc..
                );
            }
        }

        foreach ($this->boolFilters as $attributeId => $checked) {
            if ($checked) {
                $query->whereHas('globalProductAttributes', fn ($q) => $q
                    ->where('attribute_id', $attributeId)
                );
            }
        }

        foreach ($this->rangeFilters as $attributeId => $range) {
            if ($this->isRangeFiltered($attributeId)) {
                $query->whereHas('globalProductAttributes', fn ($q) => $q
                    ->where('attribute_id', $attributeId)
                    ->whereRaw('custom_value BETWEEN ? AND ?', [$range['min'], $range['max']])
                );
            }
        }

        if ($this->isPriceFiltered()) {
            $query->whereHas('variants', fn ($q) => $q
                ->whereHas('activePriceHistory', fn ($ph) => $ph
                    ->whereBetween('price', [$this->priceFilter['min'], $this->priceFilter['max']])
                )
                ->orWhereHas('activeQuantityPrices', fn ($qp) => $qp
                    ->whereBetween('price', [$this->priceFilter['min'], $this->priceFilter['max']])
                )
            );
        }

        return $query;
    }

    private function initializePriceFilter(): void
    {
        $categoryIds = $this->subCategoryIds();

        $result = PriceHistory::query()
            ->active()
            ->whereHas('variant.globalProduct', fn ($q) => $q
                ->whereIn('category_id', $categoryIds)
                ->where('global_products.is_active', true)
            )
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $min = (float) ($result->min_price ?? 0);
        $max = (float) ($result->max_price ?? 1000);

        $this->priceRange = ['min' => $min, 'max' => $max];

        if (empty($this->priceFilter)) {
            $this->priceFilter = ['min' => $min, 'max' => $max];
        }
    }

    private function initializeRangeFilters(): void
    {
        $categoryIds = $this->subCategoryIds();

        $numberAttributes = Attribute::query()
            ->where('is_filterable', true)
            ->where('type', 'number')
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->pluck('id');

        $range = GlobalProductAttribute::query()
            ->whereIn('attribute_id', $numberAttributes)
            ->whereNotNull('custom_value')
            ->whereHas('globalProduct', fn ($q) => $q
                ->whereIn('category_id', $categoryIds)
                ->where('is_active', true)
            )
            ->selectRaw('attribute_id, MIN(custom_value) as min_val, MAX(custom_value) as max_val')
            ->groupBy('attribute_id')
            ->get();

        foreach ($range as $item) {
            $this->attributeRanges[$item->attribute_id] = ['min' => (float) $item->min_val, 'max' => (float) $item->max_val];

            if (! isset($this->rangeFilters[$item->attribute_id])) {
                $this->rangeFilters[$item->attribute_id] = ['min' => (float) $item->min_val, 'max' => (float) $item->max_val];
            }
        }
    }

    public function hasActiveFilters(): bool
    {
        if (! empty($this->selectedTenants) || ! empty($this->selectedValues) || ! empty(array_filter($this->boolFilters))) {
            return true;
        }

        foreach ($this->rangeFilters as $attributeId => $range) {
            if ($this->isRangeFiltered($attributeId)) {
                return true;
            }
        }

        return $this->isPriceFiltered();
    }

    public function toggleBool(int $attributeId): void
    {
        $this->boolFilters[$attributeId] = empty($this->boolFilters[$attributeId]);
        $this->resetPage();
    }

    public function toggleValue(int $attributeId): void
    {
        if (in_array($attributeId, $this->selectedValues)) {
            $this->selectedValues = array_values(array_diff($this->selectedValues, [$attributeId]));
        } else {
            $this->selectedValues[] = $attributeId;
        }
        $this->resetPage();
    }

    public function updateRange(int $attributeId, float $min, float $max): void
    {
        $this->rangeFilters[$attributeId] = ['min' => $min, 'max' => $max];
        $this->resetPage();
    }

    public function updatePrice(float $min, float $max): void
    {
        $this->priceFilter = ['min' => $min, 'max' => $max];
        $this->resetPage();
    }

    public function toggleTenant(int $tenantId): void
    {
        if (in_array($tenantId, $this->selectedTenants)) {
            $this->selectedTenants = array_values(array_diff($this->selectedTenants, [$tenantId]));
        } else {
            $this->selectedTenants[] = $tenantId;
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->selectedValues = [];
        $this->boolFilters = [];
        $this->selectedTenants = [];

        $this->rangeFilters = [];
        $this->priceFilter = [];

        $this->initializeRangeFilters();
        $this->initializePriceFilter();
        $this->resetPage();

        $this->dispatch('filters-cleared');
    }

    public function render(): View
    {
        return view('livewire.product-browser');
    }

    private function isRangeFiltered(int $attributeId): bool
    {
        if (! isset($this->attributeRanges[$attributeId])) {
            return false;
        }

        $bounds = $this->attributeRanges[$attributeId];
        $range = $this->rangeFilters[$attributeId];

        return (float) $range['min'] > $bounds['min'] || (float) $range['max'] < $bounds['max'];
    }

    private function isPriceFiltered(): bool
    {
        if (empty($this->priceRange) || empty($this->priceFilter)) {
            return false;
        }

        return (float) $this->priceFilter['min'] > $this->priceRange['min']
            || (float) $this->priceFilter['max'] < $this->priceRange['max'];
    }
}
