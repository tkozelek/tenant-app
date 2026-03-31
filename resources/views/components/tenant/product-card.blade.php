@props(['product', 'price'])

@php
    $globalProduct = $product->globalProduct;
    $cheapestVariant = $product->cheapestActiveVariant();
@endphp

<div class="group relative block bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-700 transition-all duration-300">

    @if($globalProduct)
        <a href="{{ route('products.product', $globalProduct) . '#tenant-' . $product->tenant_id }}" class="absolute inset-0 z-10">
            <span class="sr-only">{{ $product->name }}</span>
        </a>
    @endif

    <div class="relative aspect-4/3 overflow-hidden bg-neutral-800">
        @if($globalProduct?->getFirstMediaUrl('global_products'))
            <img src="{{ $globalProduct->getFirstMediaUrl('global_products') }}"
                 alt="{{ $globalProduct->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-box text-3xl text-neutral-700"></i>
            </div>
        @endif
    </div>

    <div class="p-4 relative z-0">
        @if($globalProduct?->category?->name)
            <span class="text-xs text-neutral-500 font-semibold uppercase tracking-wider">
                {{ $globalProduct->category->name }}
            </span>
        @endif

        <h3 class="font-semibold text-white mt-1 mb-3 line-clamp-1">
            {{ $product->name }}
        </h3>

        @if($cheapestVariant?->currentPrice)
            <div class="flex items-baseline gap-2">
                @if($cheapestVariant->currentOriginalPrice && $cheapestVariant->currentOriginalPrice > $cheapestVariant->currentPrice)
                    <span class="text-xs text-neutral-600 line-through">
                        {{ number_format($cheapestVariant->currentOriginalPrice, 2, ',', ' ') }} €
                    </span>
                @endif
                @isset($price)
                    <span class="text-base font-bold text-white">
                        od {{ number_format($price, 2, ',', ' ') }} €
                    </span>
                @endisset
            </div>
        @endif
    </div>
</div>
