<x-app-layout>
    @section('title', 'Vysledky: ' . $query)

    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-white mb-2">Vysledky vyhladavania</h1>

        @if($categories->count() > 0)
            <div class="mb-8">
                <h2 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider mb-3">Kategorie</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        <a href="{{ route('products.show', $category) }}"
                           class="bg-neutral-800 text-neutral-300 px-4 py-2 rounded-lg hover:bg-neutral-700 hover:text-white transition-colors text-sm">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($products->count() > 0)
            <div>
                <h2 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider mb-4">Produkty</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $product)
                        <x-global-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        @endif

        @if($categories->count() === 0 && $products->count() === 0)
            <div class="text-center py-20">
                <i class="fa-solid fa-magnifying-glass text-4xl text-neutral-700 mb-4"></i>
                <p class="text-neutral-500">Ziadne vysledky pre "{{ $query }}"</p>
            </div>
        @endif
    </div>
</x-app-layout>
