<x-app-layout>
    @section('title', 'Produkty')

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <h1 class="text-2xl font-bold text-white mb-8">Katalóg produktov</h1>

        <div class="space-y-2 border-b border-neutral-800 pb-10 mb-10">
            @foreach($categories as $category)
                <div class="flex items-stretch gap-6 rounded-xl border border-neutral-800 bg-neutral-900 overflow-hidden hover:border-neutral-700 transition-colors duration-200">

                    <a href="{{ route('products.show', $category) }}"
                       class="group flex flex-col justify-center w-48 shrink-0 px-6 py-5 bg-neutral-800 hover:bg-neutral-700 transition-colors duration-200">
                        <span class="text-base font-bold text-white group-hover:text-neutral-200 transition-colors leading-tight">
                            {{ $category->name }}
                        </span>
                        <span class="text-xs text-neutral-500 mt-1 group-hover:text-neutral-400 transition-colors">
                            Zobraziť všetky
                        </span>
                    </a>

                    <div class="flex items-center flex-wrap gap-2 py-4 pr-4">
                        @if($category->children->count())
                            @foreach($category->children as $child)
                                <a href="{{ route('products.show', $child) }}"
                                   class="text-sm font-medium text-neutral-300 bg-neutral-800 hover:bg-neutral-700 hover:text-white px-4 py-2 rounded-lg transition-colors duration-150">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        @else
                            <span class="text-sm text-neutral-600 italic">Žiadne podkategórie</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

<h2 class="text-xl font-bold text-white mb-5">Najnovšie produkty</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($products as $product)
                <x-global-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
