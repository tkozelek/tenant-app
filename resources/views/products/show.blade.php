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
                <aside class="lg:block w-72 shrink-0">
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
    </div>
</x-app-layout>
