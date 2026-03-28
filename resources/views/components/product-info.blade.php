@props(['product'])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <div class="aspect-4/3 bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden">
            @if($product->getFirstMediaUrl('global_products'))
                <img src="{{ $product->getFirstMediaUrl('global_products') }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
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

        @if($product->description)
            <div class="mt-10 prose prose-invert prose-neutral max-w-none">
                {!! $product->description !!}
            </div>
        @endif
    </div>
</div>
