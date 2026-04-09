@props(['variant'])

@if($variant->activePriceHistory?->is_flash_sale)
    <div class="flex flex-col items-end gap-0.5"
         x-data="flashSaleCountdown({{ $variant->activePriceHistory->valid_to->timestamp * 1000 }})">
        <span class="text-xs text-orange-500">
            <i class="fa-solid fa-bolt"></i>
            {{ $variant->activePriceHistory->flash_sale_label ?? 'Flash sale' }}
        </span>
        @if($variant->currentOriginalPrice && $variant->currentOriginalPrice > $variant->currentPrice)
            <span class="text-xs text-neutral-600 line-through">{{ $variant->current_original_price_formatted }}</span>
        @endif
        <span class="font-semibold text-orange-400">{{ $variant->current_price_formatted }}</span>
        <span class="text-xs text-neutral-600 font-mono" x-text="remaining"></span>
    </div>
@elseif($variant->relationLoaded('activeQuantityPrices') && $variant->activeQuantityPrices->isNotEmpty())
    <div class="flex flex-col items-end gap-0.5">
        @foreach($variant->activeQuantityPrices as $tier)
            <span class="text-xs text-neutral-400">
                {{ $tier->min_quantity }} {{ $tier->max_quantity ? '-'.$tier->max_quantity : '+' }} ks:
                <span class="text-white">{{ number_format( $tier->price, 2, ',', ' ') }} €</span>
            </span>
        @endforeach
        @if($variant->current_price)
            <span class="text-xs text-neutral-600">bežná: {{ $variant->current_price_formatted }}</span>
        @endif
    </div>
@elseif($variant->currentOriginalPrice && $variant->currentOriginalPrice > $variant->currentPrice)
    <span class="text-xs text-neutral-600 line-through mr-1">{{ $variant->current_original_price_formatted }}</span>
    <span class="font-semibold text-white">{{ $variant->current_price_formatted }}</span>
@else
    <span class="font-semibold text-white">{{ $variant->current_price_formatted }}</span>
@endif
