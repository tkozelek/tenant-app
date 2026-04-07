@props(['bundle'])

<div class="flex flex-col bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-700 transition-colors duration-200">

    <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-4 border-b border-neutral-800">
        <div class="min-w-0">
            <h3 class="font-semibold text-white leading-tight truncate">{{ $bundle->name }}</h3>
            <span class="text-xs text-neutral-500 mt-0.5 block">{{ $bundle->tenant->name }}</span>
        </div>

        <div class="text-right shrink-0">
            @if($bundle->original_price && $bundle->original_price > $bundle->price)
                <span class="text-xs text-neutral-500 line-through block">
                    {{ number_format($bundle->original_price, 2, ',', ' ') }} €
                </span>
                <span class="text-base font-bold text-red-400">
                    {{ number_format($bundle->price, 2, ',', ' ') }} €
                </span>
                <span class="text-xs text-emerald-500 font-medium block mt-0.5">
                    ušetríte {{ number_format($bundle->original_price - $bundle->price, 2, ',', ' ') }} €
                </span>
            @else
                <span class="text-base font-bold text-white">
                    {{ number_format($bundle->price, 2, ',', ' ') }} €
                </span>
            @endif
        </div>
    </div>

    <div class="divide-y divide-neutral-800/70 flex-1">
        @foreach($bundle->items as $item)
            <div class="flex items-center justify-between gap-3 px-5 py-2.5">
                <span class="text-sm text-neutral-300 truncate">
                    <span class="text-neutral-600 text-xs mr-1">{{ $item->quantity }}×</span>
                    {{ $item->variant->name }}
                </span>

                <span class="text-sm text-neutral-400 shrink-0">
                    @if($item->variant->currentPrice)
                        {{ $item->variant->current_price_formatted }}
                    @else
                        <span class="text-neutral-600">—</span>
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>
