<?php

namespace App\Livewire;

use App\Models\TenantProductVariant;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class VariantPriceHistory extends Component
{
    public ?int $variantId = null;

    #[On('select-variant-history')]
    public function selectVariant(int $variantId): void
    {
        $this->variantId = $variantId;
        $this->dispatch('open-modal', 'variant-history');
    }

    public function render(): View
    {
        $variant = $this->variantId
            ? TenantProductVariant::with('product.tenant', 'product.globalProduct')->find($this->variantId)
            : null;

        $history = $variant
            ? $variant->priceHistories()->with('user:id,email')->latest('created_at')->get()
            : collect();

        return view('livewire.variant-price-history', compact('variant', 'history'));
    }
}
