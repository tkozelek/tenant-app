<?php

namespace App\Livewire;

use App\Models\Bundle;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BundleBrowser extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $selectedTenant = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedTenant(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::query()->where('is_public', true)->orderBy('name')->get();
    }

    #[Computed]
    public function bundles(): LengthAwarePaginator
    {
        return Bundle::query()
            ->with(['tenant', 'items.variant.activePriceHistory'])
            ->where('is_active', true)
            ->when($this->selectedTenant, fn ($q) => $q->where('tenant_id', $this->selectedTenant))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(12);
    }

    public function render(): View
    {
        return view('livewire.bundle-browser');
    }
}
