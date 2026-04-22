<x-app-layout>
    @section('title', 'Porovnanie produktov')
    <div class="container mx-auto px-4 py-8" x-data="compareApp">
        <div class="flex flex-wrap items-center gap-2 mb-8">
            <h1 class="text-lg font-semibold text-white mr-2">Porovnanie</h1>

            @foreach($products as $product)
                <div class="flex items-center gap-1.5 border border-neutral-700 rounded-md px-2.5 py-1">
                    <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $product['color'] }}"></span>
                    <span class="text-sm text-neutral-300">{{ $product['name'] }}</span>
                    @if(isset($product['subtitle']) && $product['subtitle'])
                        <span class="text-xs text-neutral-600">{{ $product['subtitle'] }}</span>
                    @endif
                    <button
                        @click="removeProduct('{{ $product['slug'] }}')"
                        class="text-neutral-600 hover:text-neutral-300 transition-colors ml-0.5"
                    >
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
            @endforeach

            @if(count($slugs) < $maxProducts)
                <div class="relative">
                    <div class="flex items-center gap-1.5 border border-dashed border-neutral-700 rounded-md px-2.5 py-1 focus-within:border-neutral-500 transition-colors">
                        <i class="fa-solid fa-plus text-neutral-600 text-xs"></i>
                        <input
                            type="text"
                            x-model="search"
                            @keyup.debounce.300ms="doSearch()"
                            @keydown.escape="searchOpen = false"
                            @click.outside="searchOpen = false"
                            :placeholder="mode === 'variant' ? 'Pridať variant...' : 'Pridať produkt...'"
                            class="bg-transparent text-sm text-white placeholder-neutral-600 outline-none w-40"
                        />
                    </div>

                    <div
                        x-show="searchOpen"
                        class="absolute top-full left-0 mt-1 w-72 bg-neutral-900 border border-neutral-800 rounded-lg shadow-xl z-20 overflow-hidden max-h-48 overflow-y-auto"
                    >
                        <template x-for="result in results" :key="result.slug">
                            <button
                                @click="addProduct(result.slug); searchOpen = false; search = ''"
                                class="w-full text-left px-3 py-2 text-sm text-neutral-300 hover:bg-neutral-800 hover:text-white transition-colors"
                                x-text="result.name"
                            ></button>
                        </template>
                    </div>
                </div>
            @endif

            {{-- Mode toggle --}}
            <label class="flex items-center gap-2 ml-auto cursor-pointer select-none">
                <span class="text-sm text-neutral-400">Varianty</span>
                <input
                    type="checkbox"
                    :checked="mode === 'variant'"
                    @change="toggleMode()"
                    class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-blue-500 cursor-pointer"
                >
            </label>
        </div>

        @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <i class="fa-solid fa-scale-balanced text-4xl text-neutral-700 mb-4"></i>
                <p class="text-neutral-400 mb-1">Žiadne produkty na porovnanie</p>
                <p class="text-neutral-600 text-sm">
                    {{ $mode === 'variant' ? 'Vyhľadaj variant podľa názvu, SKU alebo EAN kódu' : 'Vyhľadaj produkt vyššie alebo pridaj produkty z katalógu' }}
                </p>
            </div>
        @else
            <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-4 sm:p-6" style="height: 480px">
                <canvas id="compareChart"></canvas>
            </div>

            <script type="application/json" id="compare-chart-data">
                {!! json_encode($chartData) !!}
            </script>
        @endif
    </div>
</x-app-layout>
<script>
    const productSlugs = @json($slugs);
    const compareMode = @json($mode);

    document.addEventListener('alpine:init', () => {
        Alpine.data('compareApp', () => ({
            search: '',
            results: [],
            searchOpen: false,
            searchLoading: false,
            mode: compareMode,

            init() {
                initCompareChart();
            },

            async doSearch() {
                const q = this.search.trim();

                if (q.length < 2) {
                    this.results = [];
                    this.searchOpen = false;
                    return;
                }

                this.searchLoading = true;

                try {
                    const params = new URLSearchParams({ q, mode: this.mode });

                    const res = await fetch('/porovnat/search?' + params.toString());
                    this.results = await res.json();
                    this.searchOpen = this.results.length > 0;
                } finally {
                    this.searchLoading = false;
                }
            },

            addProduct(slug) {
                const url = new URL(window.location.href);
                const param = this.mode === 'variant' ? 'ids[]' : 'slugs[]';
                url.searchParams.append(param, slug);
                window.location.href = url.toString();
            },

            removeProduct(slug) {
                const url = new URL(window.location.href);
                const param = this.mode === 'variant' ? 'ids[]' : 'slugs[]';
                const current = url.searchParams.getAll(param).filter(s => s !== String(slug));
                url.searchParams.delete(param);
                current.forEach(s => url.searchParams.append(param, s));
                window.location.href = url.toString();
            },

            toggleMode() {
                const newMode = this.mode === 'product' ? 'variant' : 'product';
                const url = new URL(window.location.href);
                // clear all products when switching modes - slugs and ids are incompatible
                url.search = '';
                if (newMode === 'variant') {
                    url.searchParams.set('mode', 'variant');
                }
                window.location.href = url.toString();
            },
        }));
    });
</script>
