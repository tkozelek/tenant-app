<x-app-layout>
    @section('title', $globalProduct->name)

    <div class="container mx-auto px-4 py-6">
        <x-breadcrumbs :items="[
            ['label' => 'Produkty', 'url' => route('products.index')],
            ...($globalProduct->category ? [['label' => $globalProduct->category->name, 'url' => route('products.show', $globalProduct->category)]] : []),
            ['label' => $globalProduct->name],
        ]"/>

        <x-product-info :product="$globalProduct"/>

        {{-- https://www.penguinui.com/components/tabs --}}
        <div x-data="{ tab: '{{ $groupedAttributes?->isNotEmpty() ? 'tenants' : 'parameters' }}' }" class="mt-10">

            <div class="text-sm font-medium text-center text-neutral-400 border-b border-neutral-800">
                <ul class="flex flex-wrap -mb-px">
                    @if($globalProduct->tenantProducts->count() > 0)
                        <li class="me-2">
                            <button href="#"
                                    x-on:click="tab = 'tenants'"
                                    x-bind:class="tab === 'tenants'
                                       ? 'text-white border-b border-white'
                                       : 'border-b border-transparent hover:text-neutral-200 hover:border-neutral-400'"
                                    class="inline-block p-4 rounded-t-lg transition-colors"
                            >
                                Kde kúpiť
                                <span class="ml-1.5 text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full">
                                    {{ $globalProduct->tenantProducts->count() }}
                                </span>
                            </button>
                        </li>
                    @endif

                    @if($groupedAttributes?->isNotEmpty())
                        <li class="me-2">
                            <button href="#"
                                    x-on:click="tab = 'parameters'"
                                    x-bind:class="tab === 'parameters'
                                       ? 'text-white border-b border-white'
                                       : 'border-b border-transparent hover:text-neutral-200 hover:border-neutral-400'"
                                    class="inline-block p-4 rounded-t-lg transition-colors"
                            >
                                Parametre
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            @if($globalProduct->tenantProducts->count() > 0)
                <div x-show="tab === 'tenants'" x-transition class="mt-6 space-y-3">
                    @foreach($globalProduct->tenantProducts as $tenantProduct)
                        <x-tenant-product-accordion :tenantProduct="$tenantProduct"/>
                    @endforeach
                </div>
            @endif

            @if($groupedAttributes?->isNotEmpty())
                <div x-show="tab === 'parameters'" x-transition class="mt-6">
                    <x-product-attributes :groupedAttributes="$groupedAttributes"/>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
