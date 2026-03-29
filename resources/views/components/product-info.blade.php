@props(['product', 'lowestPrice' => null])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <x-media-thumbnail
            :url="$product->getFirstMediaUrl('global_products')"
            :alt="$product->name"
            icon="fa-box"
            :cover="false"
            class="aspect-auto bg-neutral-900 border border-neutral-800 rounded-xl"
        />
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
