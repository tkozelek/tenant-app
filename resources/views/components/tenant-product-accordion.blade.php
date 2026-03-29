@props(['tenantProduct'])

@php
    $lowestPrice = $tenantProduct->variants
        ->map(fn ($v) => $v->current_price)
        ->min();
@endphp

<x-accordion
    open="highlightedTenant === 'tenant-{{ $tenantProduct->tenant_id }}'"
    :id="'tenant-'.$tenantProduct->tenant_id"
    x-bind:class="highlightedTenant === 'tenant-{{ $tenantProduct->tenant_id }}' && 'ring-1 ring-white/20'"
>
    <x-slot name="header">
        <x-media-thumbnail
            :url="$tenantProduct->tenant->getImageUrl()"
            :alt="$tenantProduct->tenant->name"
            icon="fa-store"
            class="w-10 h-10 rounded-lg"
        />
        <div>
            <span class="font-semibold text-white block leading-tight">{{ $tenantProduct->tenant->name }}</span>
        </div>
        <span class="text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full shrink-0">
            {{ $tenantProduct->variants->count() }} - variant
        </span>
    </x-slot>

    <x-slot name="aside">
        @if($lowestPrice)
            <span class="text-sm font-semibold text-white">od {{ number_format($lowestPrice, 2, ',', ' ') }} €</span>
        @endif
    </x-slot>

    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-neutral-500 border-b border-neutral-800">
                <th class="px-5 py-2 font-medium">Variant</th>
                <th class="px-5 py-2 font-medium">SKU</th>
                <th class="px-5 py-2 font-medium">Sklad</th>
                <th class="px-5 py-2 font-medium text-right">Cena</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800">
            @foreach($tenantProduct->variants as $variant)
                <tr class="hover:bg-neutral-800/30 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <x-media-thumbnail
                                :url="$variant->getFirstMediaUrl('tenant_product_variants')"
                                :alt="$variant->name"
                                icon="fa-cube"
                                class="w-8 h-8 rounded"
                            />
                            <span class="text-white">{{ $variant->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-neutral-400 font-mono text-xs">{{ $variant->sku ?? '-' }}</td>
                    <td class="px-5 py-3">
                        @if($variant->stock_quantity > 0)
                            <span class="text-green-400">{{ $variant->stock_quantity }} ks</span>
                        @else
                            <span class="text-red-400">Vypredané</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($variant->currentOriginalPrice && $variant->currentOriginalPrice > $variant->currentPrice)
                            <span class="text-xs text-neutral-500 line-through mr-1">{{ $variant->current_original_price_formatted }}</span>
                            <span class="font-semibold text-red-400">{{ $variant->current_price_formatted }}</span>
                        @else
                            <span class="font-semibold text-white">{{ $variant->current_price_formatted }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-accordion>
