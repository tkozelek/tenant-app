<?php

namespace App\Livewire;

use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CouponBrowser extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $selectedTenant = '';

    public ?int $openCouponId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedTenant(): void
    {
        $this->resetPage();
    }

    public function selectCoupon(int $id): void
    {
        $this->openCouponId = $id;
        $this->dispatch('open-modal', 'coupon-detail');
    }

    public function closeModal(): void
    {
        $this->openCouponId = null;
    }

    #[Computed]
    public function openCoupon(): ?Coupon
    {
        if (! $this->openCouponId) {
            return null;
        }

        return Coupon::with([
            'tenant',
            'productVariants.product.globalProduct',
            'productVariants.product.tenant',
            'categories',
        ])->find($this->openCouponId);
    }

    #[Computed]
    public function tenants(): Collection
    {
        return Tenant::query()->where('is_public', true)->orderBy('name')->get();
    }

    #[Computed]
    public function coupons(): LengthAwarePaginator
    {
        return Coupon::query()
            ->with(['tenant', 'productVariants.product.globalProduct', 'categories'])
            ->where('is_active', true)
            ->when($this->selectedTenant, fn ($q) => $q->where('tenant_id', $this->selectedTenant))
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->whereAny(['code', 'description'], 'like', "%{$search}%")
                    ->orWhereHas('productVariants.product.globalProduct', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.coupon-browser');
    }
}
