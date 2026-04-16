<x-app-layout>
@section('meta.description', $tenant->short_description ?? null)
    @section('og.title', $tenant->name ?? null)
    @section('og.description', $tenant->short_description ?? null)
    @section('og.url', request()->url() ?? null)

    <div class="min-h-screen bg-neutral-950">
        <x-tenant.hero :tenant="$tenant" />

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col gap-12">
            @if(!empty($tenant->description))
                <x-tenant.about :tenant="$tenant" />
            @endif

            @if($products->count() > 0)
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider">Najnovšie produkty</h2>
                        <div class="flex items-center gap-2" x-data x-init>
                            <button @click="$dispatch('embla-products-prev')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full border border-neutral-800 text-neutral-500 hover:border-neutral-600 hover:text-white transition-colors">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                            </button>
                            <button @click="$dispatch('embla-products-next')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full border border-neutral-800 text-neutral-500 hover:border-neutral-600 hover:text-white transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div
                        x-data="{
                            embla: null,
                            init() {
                                this.embla = EmblaCarousel(this.$el, {
                                    loop: false,
                                    align: 'start',
                                    slidesToScroll: 1,
                                });
                                window.addEventListener('embla-products-prev', () => this.embla.scrollPrev());
                                window.addEventListener('embla-products-next', () => this.embla.scrollNext());
                            }
                        }"
                        class="overflow-hidden"
                    >
                        <div class="flex gap-4">
                            @foreach($products as $product)
                                <div class="shrink-0 w-56 sm:w-64">
                                    <x-tenant.product-card :product="$product" :price="$lowestPrices[$product->id] ?? null" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($bundles->count())
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider">Balíčky</h2>
                        <div class="flex items-center gap-2" x-data x-init>
                            <button @click="$dispatch('embla-bundles-prev')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full border border-neutral-800 text-neutral-500 hover:border-neutral-600 hover:text-white transition-colors">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                            </button>
                            <button @click="$dispatch('embla-bundles-next')"
                                    class="w-8 h-8 flex items-center justify-center rounded-full border border-neutral-800 text-neutral-500 hover:border-neutral-600 hover:text-white transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div
                        x-data="{
                            embla: null,
                            init() {
                                this.embla = EmblaCarousel(this.$el, {
                                    loop: false,
                                    align: 'start',
                                    slidesToScroll: 1,
                                });
                                window.addEventListener('embla-bundles-prev', () => this.embla.scrollPrev());
                                window.addEventListener('embla-bundles-next', () => this.embla.scrollNext());
                            }
                        }"
                        class="overflow-hidden"
                    >
                        <div class="flex gap-4">
                            @foreach($bundles as $bundle)
                                <div class="shrink-0 w-56 sm:w-64">
                                    <div class="group block bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-700 transition-all duration-300 h-full">
                                        <div class="relative aspect-4/3 overflow-hidden bg-neutral-800">
                                            @if($bundle->getFirstMediaUrl('bundles'))
                                                <img src="{{ $bundle->getFirstMediaUrl('bundles') }}"
                                                     alt="{{ $bundle->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fa-solid fa-boxes-stacked text-3xl text-neutral-700"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-4">
                                            <h3 class="font-semibold text-white mb-3 line-clamp-1">{{ $bundle->name }}</h3>

                                            @if($bundle->currentPrice)
                                                <div class="flex items-baseline gap-2">
                                                    @if($bundle->currentOriginalPrice && $bundle->currentOriginalPrice > $bundle->currentPrice)
                                                        <span class="text-xs text-neutral-600 line-through">{{ $bundle->current_original_price_formatted }}</span>
                                                    @endif
                                                    <span class="text-base font-bold text-white">{{ $bundle->current_price_formatted }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
