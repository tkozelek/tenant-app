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

        <livewire:product-browser :categoryId="$category->id" />
    </div>
</x-app-layout>
