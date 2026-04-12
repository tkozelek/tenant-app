<div>
    <x-modal name="variant-history" max-width="2xl">
        @if($variant)
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-800">
                <div>
                    <p class="text-xs text-neutral-500">{{ $variant->product->tenant->name }} &mdash; {{ $variant->product->globalProduct->name }}</p>
                    <h3 class="text-white font-semibold">{{ $variant->name }}</h3>
                </div>
                <button @click="$dispatch('close-modal', 'variant-history')" class="text-neutral-500 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="p-6">
            @if($history->isEmpty())
                <p class="text-sm text-neutral-500">Žiadna história cien.</p>
            @else
                <div wire:key="chart-{{ $variantId }}"
                     class="h-64"
                     x-data
                     x-init="$nextTick(() => initVariantPriceChart($refs.variantChart))">
                    <canvas x-ref="variantChart"
                            data-labels="{{ json_encode($history->map(fn ($e) => $e->created_at->format('d.m.Y'))->values()) }}"
                            data-prices="{{ json_encode($history->map(fn ($e) => (float) $e->price)->values()) }}"
                            data-original="{{ json_encode($history->map(fn ($e) => $e->original_price ? (float) $e->original_price : null)->values()) }}">
                    </canvas>
                </div>
            @endif
        </div>
    </x-modal>
</div>
