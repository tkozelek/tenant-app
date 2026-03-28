@props(['tenantProduct'])

@php
    $lowestPrice = $tenantProduct->variants
        ->map(fn ($v) => $v->current_price)
        ->filter()
        ->min();
@endphp

<div x-data="{ open: false }" class="bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden">
    <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-neutral-800 transition-colors">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-neutral-800 border border-neutral-700 overflow-hidden shrink-0 flex items-center justify-center">
                @if($tenantProduct->tenant->getImageUrl())
                    <img src="{{ $tenantProduct->tenant->getImageUrl() }}" alt="{{ $tenantProduct->tenant->name }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-store text-neutral-600 text-sm"></i>
                @endif
            </div>

            <div>
                <span class="font-semibold text-white block leading-tight">{{ $tenantProduct->tenant->name }}</span>
            </div>

            <span class="text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full shrink-0">
                {{ $tenantProduct->variants->count() }} - variant
            </span>
        </div>

        <div class="flex items-center gap-4 shrink-0 ml-4">
            @if($lowestPrice)
                <span class="text-sm font-semibold text-white">od {{ number_format($lowestPrice, 2, ',', ' ') }} €</span>
            @endif
            <i class="fa-solid fa-chevron-down text-neutral-500 transition-transform duration-200" :class="open && 'rotate-180'"></i>
        </div>
    </button>

    <div x-show="open" x-transition class="border-t border-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-neutral-500 border-b border-neutral-800">
                        <th class="px-5 py-2 font-medium">Variant</th>
                        <th class="px-5 py-2 font-medium">SKU</th>
                        <th class="px-5 py-2 font-medium">Sklad</th>
                        <th class="px-5 py-2 font-medium text-right">Cena</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @foreach($tenantProduct->variants as $variant)
                        <tr class="hover:bg-neutral-800/30 transition-colors">
                            <td class="px-5 py-3 text-white">{{ $variant->name }}</td>
                            <td class="px-5 py-3 text-neutral-400 font-mono text-xs">{{ $variant->sku ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @if($variant->stock_quantity > 0)
                                    <span class="text-green-400">{{ $variant->stock_quantity }} ks</span>
                                @else
                                    <span class="text-red-400">Vypredané</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-white">
                                {{ $variant->current_price_formatted }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
