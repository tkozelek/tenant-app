<x-app-layout>
    @section('title', 'Produkty')

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <h1 class="text-2xl font-bold text-white mb-8">Kategorie</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 border-b-2 border-neutral-600 pb-5">
            @foreach($categories as $category)
                <a href="{{ route('products.show', $category) }}"
                   class="group bg-neutral-900 border border-neutral-800 rounded-xl p-6 hover:border-neutral-600 transition-all duration-300">
                    <h3 class="text-lg font-bold text-white mb-3 group-hover:text-neutral-300 transition-colors">
                        {{ $category->name }}
                    </h3>

                    {{-- ak ma kategoria podkategorie, zobrazim ich ako tagy --}}
                    @if($category->children->count())
                        <div class="flex flex-wrap gap-2">
                            @foreach($category->children as $child)
                                <span class="text-xs bg-neutral-800 text-neutral-400 px-2.5 py-1 rounded-full group-hover:bg-neutral-700 transition-colors">
                                    {{ $child->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        <h2 class="text-2xl font-bold text-white mt-5 mb-5">Najnovšie produkty</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <x-global-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
