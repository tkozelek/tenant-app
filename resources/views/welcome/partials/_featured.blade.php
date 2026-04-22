@if($featuredProducts->isNotEmpty())
<section class="py-24 bg-white dark:bg-neutral-950">
    <div class="container mx-auto px-4">

        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-white mb-2">
                    Odporúčané produkty
                </h2>
                <p class="text-neutral-500 dark:text-neutral-400">
                    Výber produktov, ktoré stoja za pozornosť.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0" x-data x-init>
                <button @click="$dispatch('embla-prev')"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 text-neutral-500 dark:text-neutral-400 hover:border-neutral-400 dark:hover:border-neutral-600 hover:text-neutral-900 dark:hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </button>
                <button @click="$dispatch('embla-next')"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 text-neutral-500 dark:text-neutral-400 hover:border-neutral-400 dark:hover:border-neutral-600 hover:text-neutral-900 dark:hover:text-white transition-colors">
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
                    window.addEventListener('embla-prev', () => this.embla.scrollPrev());
                    window.addEventListener('embla-next', () => this.embla.scrollNext());
                }
            }"
            class="overflow-hidden"
        >
            <div class="flex gap-5">
                @foreach($featuredProducts as $product)
                    <div class="shrink-0 w-64">
                        <a href="{{ route('products.product', $product) }}"
                           class="group block bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors h-full">

                            <div class="aspect-square bg-neutral-100 dark:bg-neutral-800 overflow-hidden">
                                <x-media-thumbnail
                                    :url="$product->getFirstMediaUrl('global_products')"
                                    :alt="$product->name"
                                    icon="fa-box"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                            </div>

                            <div class="p-4">
                                @if($product->category)
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $product->category->name }}</span>
                                @endif
                                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mt-1 leading-snug group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                @if($featuredPrices[$product->id] ?? null)
                                    <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                        od <span class="text-neutral-900 dark:text-white font-semibold">{{ number_format($featuredPrices[$product->id], 2, ',', ' ') }} €</span>
                                    </p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">
                Zobraziť všetky produkty
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

    </div>
</section>
@endif
