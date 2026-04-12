@props(['bundle'])

<x-accordion>
    <x-slot name="header">
        <x-media-thumbnail
            :url="$bundle->getFirstMediaUrl('bundles')"
            :alt="$bundle->name"
            icon="fa-box-open"
            class="w-10 h-10 rounded-lg"
        />
        <div>
            <span class="font-semibold text-white block leading-tight">{{ $bundle->name }}</span>
            <span class="text-xs text-neutral-500">{{ $bundle->tenant->name }}</span>
        </div>
        <span class="text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full shrink-0">
            {{ $bundle->items->count() }} položiek
        </span>
    </x-slot>

    <x-slot name="aside">
        @if($bundle->original_price && $bundle->original_price > $bundle->price)
            <div class="text-right">
                <span class="text-xs text-neutral-500 line-through block">{{ number_format($bundle->original_price, 2, ',', ' ') }} €</span>
                <span class="text-sm font-semibold text-red-400">{{ number_format($bundle->price, 2, ',', ' ') }} €</span>
            </div>
        @else
            <span class="text-sm font-semibold text-white">{{ number_format($bundle->price, 2, ',', ' ') }} €</span>
        @endif

        @if($bundle->url)
            <a href="{{ $bundle->url }}"
               class="font-semibold tracking-wider text-xs text-neutral-800 hover:text-black transition-colors py-3 px-1.5 bg-yellow-400 hover:bg-yellow-600 rounded-lg">
                Kúpiť
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        @endif
    </x-slot>

    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-neutral-500 border-b border-neutral-800">
                <th class="px-5 py-2 font-medium">Variant</th>
                <th class="px-5 py-2 font-medium">Množstvo</th>
                <th class="px-5 py-2 font-medium text-right">Cena variantu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800">
            @foreach($bundle->items as $item)
                <tr class="hover:bg-neutral-800/30 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <x-media-thumbnail
                                :url="$item->variant->getFirstMediaUrl('tenant_product_variants')"
                                :alt="$item->variant->name"
                                icon="fa-cube"
                                class="w-8 h-8 rounded"
                            />
                            <span class="text-white">{{ $item->variant->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-neutral-400">{{ $item->quantity }} ks</td>
                    <td class="px-5 py-3 text-right">
                        @if($item->variant->currentOriginalPrice && $item->variant->currentOriginalPrice > $item->variant->currentPrice)
                            <span class="text-xs text-neutral-500 line-through mr-1">{{ $item->variant->current_original_price_formatted }}</span>
                            <span class="font-semibold text-red-400">{{ $item->variant->current_price_formatted }}</span>
                        @else
                            <span class="font-semibold text-white">{{ $item->variant->current_price_formatted }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-accordion>
