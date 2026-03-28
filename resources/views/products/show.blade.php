<x-app-layout>
    @section('title', $category->name)

    <div class="container mx-auto px-4 py-6">
        <x-breadcrumbs :items="[
            ['label' => 'Produkty', 'url' => route('products.index')], // 1.
            ...($category->parent ? [['label' => $category->parent->name, 'url' => route('products.show', $category->parent)]] : []), //2.
            ['label' => $category->name], // 3.
        ]" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white mb-4">{{ $category->name }}</h1>

            {{-- subcats --}}
            @if($category->children->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($category->children as $sub)
                        <a href="{{ route('products.show', $sub) }}"
                           class="text-sm bg-neutral-800 font-medium text-neutral-300 px-3 py-1.5 rounded-lg hover:bg-neutral-700 hover:text-white transition-colors">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex gap-8">
            @if($attributes->count() > 0)
                <aside class="hidden lg:block w-72 shrink-0">
                    <div class="sticky top-20 max-h-screen overflow-y-auto overflow-x-hidden space-y-6 pb-8 pr-2">
                        @include('products.partials._filters')
                    </div>
                </aside>
            @endif

            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-neutral-500">{{ $products->total() }} produktov</p>
                </div>

                @if($products->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($products as $product)
                            <x-global-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <i class="fa-solid fa-box-open text-4xl text-neutral-700 mb-4"></i>
                        <p class="text-neutral-500">V tejto kategorii nie su ziadne produkty</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- mobil --}}
        @if($attributes->count() > 0)
            <div x-data="{ filterOpen: false }" class="lg:hidden">
                <button
                    @click="filterOpen = true"
                    class="fixed bottom-4 right-4 z-40 bg-white text-neutral-900 px-4 py-3 rounded-full shadow-lg font-semibold text-sm flex items-center gap-2"
                >
                    <i class="fa-solid fa-sliders"></i>
                    Filtre
                </button>

                <div
                    x-show="filterOpen"
                    x-transition
                    @keydown.escape.window="filterOpen = false"
                    class="fixed inset-0 z-50 bg-neutral-950 overflow-y-auto"
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
            </div>
        @endif
    </div>
</x-app-layout>
