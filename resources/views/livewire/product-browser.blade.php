<div x-data="{ filterOpen: false }">
    <div class="flex gap-8">
        @if($this->filterAttributes->count() > 0 || $this->tenants->count() > 1)
            <aside class="hidden lg:block w-72 shrink-0">
                <div class="sticky top-20 max-h-screen overflow-y-auto overflow-x-hidden space-y-6 pb-8 pr-2">
                    @include('products.partials._filters')
                </div>
            </aside>
        @endif

        <div class="flex-1 min-w-0 relative">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-neutral-500">
                    {{ $this->products->total() }} produktov
                    <i wire:loading class="fa-solid fa-spinner text-white animate-spin"></i>
                </p>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-xs">
                        <button wire:click="$set('sort', 'date')" class="{{ $sort === 'date' ? 'text-white' : 'text-neutral-500 hover:text-white' }} transition-colors">
                            Najnovsie
                        </button>
                        <span class="text-neutral-700">|</span>
                        <button wire:click="$set('sort', 'price')" class="{{ $sort === 'price' ? 'text-white' : 'text-neutral-500 hover:text-white' }} transition-colors">
                            Cena
                        </button>
                    </div>

                    @if($this->hasActiveFilters())
                        <button wire:click="clearFilters" class="text-xs text-neutral-400 hover:text-white transition-colors">
                            Zrusit filtre
                        </button>
                    @endif
                </div>
            </div>

            <div wire:loading.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
                @if($this->products->count())
                    <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($this->products as $product)
                            <x-global-product-card :product="$product" wire:key="product-{{ $product->id }}" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $this->products->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <i class="fa-solid fa-box-open text-4xl text-neutral-700 mb-4"></i>
                        <p class="text-neutral-500">V tejto kategorii nie su ziadne produkty</p>
                        @if($this->hasActiveFilters())
                            <button wire:click="clearFilters" class="text-lg text-neutral-300 hover:text-white transition-colors">
                                Zrusit filtre
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- mobil --}}
    @if($this->filterAttributes->count() > 0 || $this->tenants->count() > 1)
        <button
            @click="filterOpen = true"
            class="lg:hidden fixed bottom-4 right-4 z-40 bg-white text-neutral-900 px-4 py-3 rounded-full shadow-lg font-semibold text-sm flex items-center gap-2"
        >
            <i class="fa-solid fa-sliders"></i>
            Filtre
        </button>

        <div
            x-show="filterOpen"
            x-transition
            @keydown.escape.window="filterOpen = false"
            class="lg:hidden fixed inset-0 z-50 bg-neutral-950 overflow-y-auto"
        >
            <div class="flex items-center justify-between px-4 py-4 border-b border-neutral-800 sticky top-0 bg-neutral-950 z-10">
                <h3 class="text-lg font-bold text-white">Filtre</h3>
                <button @click="filterOpen = false" class="text-neutral-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="px-4 py-4 space-y-6">
                @include('products.partials._filters')
            </div>
        </div>
    @endif
</div>
