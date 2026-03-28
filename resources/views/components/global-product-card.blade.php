@props(['product'])

<a href="{{ route('products.product', $product) }}"
   class="group block bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-600 hover:shadow-lg transition-all duration-300">

    <div class="relative aspect-4/3 overflow-hidden bg-neutral-800">
        @if($product->getFirstMediaUrl('global_products'))
            <img src="{{ $product->getFirstMediaUrl('global_products') }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-box text-3xl text-neutral-700"></i>
            </div>
        @endif
    </div>

    <div class="p-4">
        <span class="text-xs text-neutral-500 font-medium uppercase tracking-wider">{{ $product->category?->name }}</span>
        <h3 class="font-semibold text-white mt-1 line-clamp-2 group-hover:text-neutral-300 transition-colors">
            {{ $product->name }}
            @if(isset($product->tenant_products_count))
                <span class="text-xs text-neutral-500 mt-2 inline-block">
                {{ $product->tenant_products_count }}
            </span>
            @endif
        </h3>

        @if($product->relationLoaded('globalProductAttributes') && $product->globalProductAttributes->count())
            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($product->globalProductAttributes->take(3) as $gpa)
                    <span class="text-[11px] bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full">
                        {{ $gpa->attributeValue?->value ?? $gpa->custom_value }}{{ $gpa->attribute?->unit ? ' ' . $gpa->attribute->unit : '' }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</a>
