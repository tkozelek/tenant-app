<?php

namespace App\Livewire\Tenant;

use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchInput extends Component
{
    public string $search = '';

    #[Computed]
    public function results(): Collection
    {
        if (mb_strlen($this->search) < 2) {
            return collect();
        }

        return collect([
            ...$this->searchCategories(),
            ...$this->searchGlobalProducts(),
            ...$this->searchTenants(),
            ...$this->searchTenantProducts(),
        ]);
    }

    private function searchTenants(): array
    {
        $tenants = Tenant::query()
            ->where('is_public', true)
            ->where('name', 'like', "%{$this->search}%")
            ->limit(3)
            ->get();

        return array_map(fn (Tenant $tenant) => [
            'type' => 'tenant',
            'label' => 'Tenant',
            'name' => $tenant->name,
            'sub' => $tenant->short_description,
            'price' => null,
            'url' => route('tenant.show', $tenant),
        ], $tenants->all());
    }

    private function searchCategories(): array
    {
        $categories = Category::query()
            ->where('name', 'like', "%{$this->search}%")
            ->limit(3)
            ->get();

        return array_map(fn (Category $category) => [
            'type' => 'category',
            'label' => 'Kategoria',
            'name' => $category->name,
            'sub' => null,
            'url' => route('products.show', $category),
        ], $categories->all());
    }

    private function searchGlobalProducts(): array
    {
        $products = GlobalProduct::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$this->search}%")
            ->with('category')
            ->limit(3)
            ->get();

        return array_map(fn (GlobalProduct $product) => [
            'type' => 'global_product',
            'label' => 'Global',
            'name' => $product->name,
            'sub' => $product->category?->name,
            'url' => route('products.product', $product),
        ], $products->all());
    }

    private function searchTenantProducts(): array
    {
        $products = TenantProduct::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$this->search}%")
            ->with('tenant')
            ->limit(3)
            ->get();

        return array_map(fn (TenantProduct $product) => [
            'type' => 'tenant_product',
            'label' => 'Tenant p.',
            'name' => $product->name,
            'sub' => $product->tenant?->name,
            'url' => route('tenant.show', $product->tenant),
        ], $products->all());
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.tenant.search-input');
    }
}
