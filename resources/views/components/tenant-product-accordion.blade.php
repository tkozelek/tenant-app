@props(['tenantProduct', 'lowestPrice' => null, 'isCheapest' => false, 'categoryCoupons' => collect()])

<x-accordion
    open="highlightedTenant === 'tenant-{{ $tenantProduct->tenant_id }}'"
    :id="'tenant-'.$tenantProduct->tenant_id"
    x-bind:class="highlightedTenant === 'tenant-{{ $tenantProduct->tenant_id }}' && 'ring-1 ring-white/20'"
    @class(['ring-1 ring-emerald-500/50' => $isCheapest])
>
    <x-slot name="header">
        <x-media-thumbnail
            :url="$tenantProduct->tenant->getImageUrl()"
            :alt="$tenantProduct->tenant->name"
            icon="fa-store"
            class="w-10 h-10 rounded-lg"
        />
        <div>
            <a href="{{ route('tenant.show', $tenantProduct->tenant) }}" class="font-semibold text-white hover:text-neutral-300 block leading-tight" target="_blank">{{ $tenantProduct->tenant->name }}</a>
        </div>
        <span class="text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full shrink-0">
            {{ $tenantProduct->variants->count() }} - variant
        </span>
        @foreach($categoryCoupons as $coupon)
            <div class="flex items-center gap-1.5 bg-yellow-900 border border-yellow-800 rounded-full px-2.5 py-0.5 shrink-0">
                <span class="font-bold text-yellow-200 text-xs tracking-wide">{{ $coupon->code }}</span>
                <span class="text-xs text-neutral-300">{{ $coupon->discount_type === 'percentage' ? number_format((float) $coupon->value, 0).'%' : number_format((float) $coupon->value, 2, ',', ' ').' €' }}</span>
                @if($coupon->min_order_amount)
                    <span class="text-xs text-neutral-300"> - min. {{ number_format((float) $coupon->min_order_amount, 2, ',', ' ') }} €</span>
                @endif
            </div>
        @endforeach
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
                <th class="px-5 py-2 font-medium">Kupóny</th>
                <th class="px-5 py-2 font-medium text-right">Cena</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800">
            @foreach($tenantProduct->variants as $variant)
                <tr @class(['hover:bg-neutral-800/30 transition-colors', 'bg-amber-950 ring-1 ring-inset ring-amber-700' => $variant->is_cheapest])>
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
                    <td class="px-5 py-3">
                        @if($variant->relationLoaded('activeCoupons') && $variant->activeCoupons->isNotEmpty())
                            <div class="flex flex-col gap-1">
                                @foreach($variant->activeCoupons as $coupon)
                                    <div class="flex flex-col">
                                        <span class="font-mono text-xs font-bold text-yellow-400 tracking-wide">{{ $coupon->code }}</span>
                                        <span class="text-xs text-neutral-300">
                                            {{ $coupon->discount_type === 'percentage' ? number_format((float) $coupon->value, 0).'%' : number_format((float) $coupon->value, 2, ',', ' ').' €' }} zľava
                                        </span>
                                        @if($coupon->min_order_amount)
                                            <span class="text-xs text-neutral-500">min. {{ number_format((float) $coupon->min_order_amount, 2, ',', ' ') }} €</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-neutral-600">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($variant->relationLoaded('activeQuantityPrices') && $variant->activeQuantityPrices->isNotEmpty())
                            <div class="flex flex-col items-end gap-0.5">
                                @foreach($variant->activeQuantityPrices as $tier)
                                    <span class="text-xs text-neutral-400">
                                        {{ $tier->min_quantity }}{{ $tier->max_quantity ? '–'.$tier->max_quantity : '+' }} ks:
                                        <span class="text-white font-semibold">{{ number_format((float) $tier->price, 2, ',', ' ') }} €</span>
                                    </span>
                                @endforeach
                                @if($variant->current_price)
                                    <span class="text-xs text-neutral-500 mt-0.5">bežná: {{ $variant->current_price_formatted }}</span>
                                @endif
                            </div>
                        @elseif($variant->currentOriginalPrice && $variant->currentOriginalPrice > $variant->currentPrice)
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
