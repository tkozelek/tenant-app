@if($featuredBundles->isNotEmpty())
<section class="py-24 bg-neutral-50 dark:bg-neutral-900/50">
    <div class="container mx-auto px-4">

        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-neutral-900 dark:text-white mb-2">
                    Aktuálne balíky
                </h2>
                <p class="text-neutral-500 dark:text-neutral-400">
                    Zvýhodnené sety produktov s úsporou až 18 %.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0" x-data x-init>
                <button @click="$dispatch('bundles-prev')"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-800 text-neutral-500 dark:text-neutral-400 hover:border-neutral-400 dark:hover:border-neutral-600 hover:text-neutral-900 dark:hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </button>
                <button @click="$dispatch('bundles-next')"
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
                    window.addEventListener('bundles-prev', () => this.embla.scrollPrev());
                    window.addEventListener('bundles-next', () => this.embla.scrollNext());
                }
            }"
            class="overflow-hidden"
        >
            <div class="flex gap-5 items-start">
                @foreach($featuredBundles as $bundle)
                    <div class="shrink-0 w-80">
                        <x-bundle-card :bundle="$bundle" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('bundles.index') }}"
               class="inline-flex items-center text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">
                Zobraziť všetky balíky
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

    </div>
</section>
@endif
