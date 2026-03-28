@props(['product'])

@php
    $lowestPrice = $product->relationLoaded('tenantProducts')
        ? $product->tenantProducts
            ->flatMap->variants
            ->map(fn ($v) => $v->current_price)
            ->filter()
            ->min()
        : null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <div class="aspect-auto bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden">
            @if($product->getFirstMediaUrl('global_products'))
                <img lazy src="{{ $product->getFirstMediaUrl('global_products') }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-box text-6xl text-neutral-700"></i>
                </div>
            @endif
        </div>
    </div>

    <div>
        @if($product->category)
            <a href="{{ route('products.show', $product->category) }}" class="text-sm text-neutral-500 hover:text-white transition-colors">
                {{ $product->category->name }}
            </a>
        @endif

        <h1 class="text-3xl font-bold text-white mt-2">{{ $product->name }}</h1>

        @if($lowestPrice)
            <p class="text-neutral-400 text-sm mt-1">od <span class="text-white font-semibold text-lg">{{ number_format($lowestPrice, 2, ',', ' ') }} €</span></p>
        @endif

        @if($product->description)
            <div class="mt-10 prose prose-invert prose-neutral max-w-none">
                {!! $product->description !!}
            </div>
        @endif
    </div>
</div>
