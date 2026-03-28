@props(['product'])

@php
    $globalProduct = $product->globalProduct;
    $cheapestVariant = $product->variants
        ->filter(fn ($v) => $v->stock_quantity > 0 && $v->activePriceHistory)
        ->sortBy(fn ($v) => $v->activePriceHistory->price)
        ->first();
    $price = $cheapestVariant?->currentPrice;
    $originalPrice = $cheapestVariant?->currentOriginalPrice;
@endphp

<a href="{{ route('products.product', $globalProduct) }}#tenant-{{ $product->tenant_id }}"
   class="group block bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-700 transition-all duration-300">

    <div class="relative aspect-[4/3] overflow-hidden bg-neutral-800">
        @if($globalProduct->getFirstMediaUrl('global_products'))
            <img src="{{ $globalProduct->getFirstMediaUrl('global_products') }}"
                 alt="{{ $globalProduct->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-box text-3xl text-neutral-700"></i>
            </div>
        @endif
    </div>

    <div class="p-4">
        @if($globalProduct->category?->name)
            <span class="text-xs text-neutral-500 font-semibold uppercase tracking-wider">{{ $globalProduct->category->name }}</span>
        @endif
        <h3 class="font-semibold text-white mt-1 mb-3 line-clamp-1">{{ $product->name }}</h3>

        @if($price)
            <div class="flex items-baseline gap-2">
                @if($originalPrice && $originalPrice > $price)
                    <span class="text-xs text-neutral-600 line-through">{{ number_format($originalPrice, 2, ',', ' ') }} €</span>
                @endif
                <span class="text-base font-bold text-white">{{ number_format($price, 2, ',', ' ') }} €</span>
            </div>
        @endif
    </div>
</a>
