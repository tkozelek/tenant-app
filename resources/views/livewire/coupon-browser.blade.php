<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-sm"></i>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Hladat podla kodu, popisu alebo produktu..."
                class="w-full pl-9 pr-4 py-2.5 bg-neutral-900 border border-neutral-700 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-neutral-500 transition-colors"
            />
        </div>


        @if($this->tenants->count() > 1)
            <x-select
                wire:model.live="selectedTenant"
                name="selectedTenant"
                placeholder="Vsetci"
                :options="$this->tenants->pluck('name', 'id')->toArray()"
                :value="$selectedTenant"
            />
        @endif
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none" class="transition-opacity duration-200">
        @if($this->coupons->isEmpty())
            <div class="text-center py-24 text-neutral-500">
                <i class="fa-solid fa-ticket text-4xl mb-4 block"></i>
                Nenasli sa ziadne kupony.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-800">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-800 text-neutral-400 text-xs uppercase tracking-wider">
                            <th class="text-left px-4 py-3 font-medium">Kod</th>
                            <th class="text-left px-4 py-3 font-medium">Obchod</th>
                            <th class="text-left px-4 py-3 font-medium">Zlava</th>
                            <th class="text-left px-4 py-3 font-medium hidden sm:table-cell">Platnost</th>
                            <th class="text-left px-4 py-3 font-medium hidden md:table-cell">Pouzitie</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @foreach($this->coupons as $coupon)
                            <tr
                                wire:key="coupon-{{ $coupon->id }}"
                                wire:click="selectCoupon({{ $coupon->id }})"
                                class="hover:bg-neutral-800/50 transition-colors cursor-pointer"
                            >
                                <td class="px-4 py-3">
                                    <span class="font-mono font-semibold text-white tracking-wide">{{ $coupon->code }}</span>
                                    @if($coupon->description)
                                        <p class="text-xs text-neutral-500 mt-0.5">{{ $coupon->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-300">{{ $coupon->tenant->name }}</td>
                                <td class="px-4 py-3">
                                    @if($coupon->discount_type === 'percentage')
                                        <x-badge color="green">{{ number_format($coupon->value) }}%</x-badge>
                                    @else
                                        <x-badge color="blue">{{ number_format($coupon->value, 2, ',', ' ') }} €</x-badge>
                                    @endif
                                    @if($coupon->min_order_amount)
                                        <p class="text-xs text-neutral-500 mt-1">min. {{ number_format($coupon->min_order_amount, 2, ',', ' ') }} €</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell text-neutral-400 text-xs">
                                    <div>{{ $coupon->starts_at->format('d.m.Y') }}</div>
                                    <div>– {{ $coupon->expires_at->format('d.m.Y') }}</div>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell text-sm">
                                    @if($coupon->usage_limit)
                                        <span class="{{ $coupon->used_count >= $coupon->usage_limit ? 'text-red-400' : 'text-neutral-300' }}">
                                            {{ $coupon->used_count }} / {{ $coupon->usage_limit }}
                                        </span>
                                    @else
                                        <span class="text-neutral-500">{{ $coupon->used_count }}x</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-neutral-600">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $this->coupons->links() }}
            </div>
        @endif
    </div>

    <x-modal name="coupon-detail" maxWidth="4xl">
        @if($this->openCoupon)
            <div class="p-6">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="font-mono text-xl font-bold text-white tracking-widest">{{ $this->openCoupon->code }}</span>
                            @if($this->openCoupon->discount_type === 'percentage')
                                <x-badge color="green">{{ number_format($this->openCoupon->value) }}%</x-badge>
                            @else
                                <x-badge color="blue">{{ number_format($this->openCoupon->value, 2, ',', ' ') }} €</x-badge>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-neutral-300">
                            <span>{{ $this->openCoupon->tenant->name }}</span>
                            <span>{{ $this->openCoupon->starts_at->format('d.m.Y') }} - {{ $this->openCoupon->expires_at->format('d.m.Y') }}</span>
                            @if($this->openCoupon->usage_limit)
                                <span class="{{ $this->openCoupon->used_count >= $this->openCoupon->usage_limit ? 'text-red-400' : 'text-neutral-300' }}">
                                    Pouzite {{ $this->openCoupon->used_count }} z {{ $this->openCoupon->usage_limit }}
                                </span>
                            @else
                                <span>Pouzite {{ $this->openCoupon->used_count }}x</span>
                            @endif
                        </div>
                    </div>
                    <button x-on:click="$dispatch('close-modal', 'coupon-detail')" wire:click="closeModal" class="text-neutral-500 hover:text-white transition-colors shrink-0 ml-4">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                @if($this->openCoupon->productVariants->isEmpty() && $this->openCoupon->categories->isEmpty())
                    <p class="text-neutral-500 text-sm">Plati pre vsetky produkty.</p>
                @endif

                @if($this->openCoupon->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($this->openCoupon->categories as $cat)
                            <x-badge color="yellow">{{ $cat->name }}</x-badge>
                        @endforeach
                    </div>
                @endif

                @if($this->openCoupon->productVariants->isNotEmpty())
                    <div class="rounded-lg border border-neutral-800 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-neutral-800 text-neutral-500 text-xs uppercase tracking-wider">
                                    <th class="text-left px-4 py-2 font-medium">Produkt</th>
                                    <th class="text-left px-4 py-2 font-medium">Variant</th>
                                    <th class="text-left px-4 py-2 font-medium">Skladom</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-800">
                                @foreach($this->openCoupon->productVariants as $variant)
                                    <tr class="hover:bg-neutral-800/40 transition-colors">
                                        <td class="px-4 py-3">
                                            @if($variant->product?->globalProduct)
                                                <a href="{{ route('products.product', $variant->product->globalProduct) }}" class="text-white hover:text-neutral-300 transition-colors font-medium" x-on:click.stop>
                                                    {{ $variant->product->globalProduct->name }}
                                                </a>
                                            @else
                                                <span class="text-neutral-500">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-neutral-400">{{ $variant->name }}</td>
                                        <td class="px-4 py-3">
                                            @if($variant->stock_quantity > 0)
                                                <span class="text-green-400">{{ $variant->stock_quantity }} ks</span>
                                            @else
                                                <span class="text-red-400">Vypredane</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </x-modal>
</div>
