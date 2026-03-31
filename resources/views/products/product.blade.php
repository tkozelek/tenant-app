<x-app-layout>
    @section('title', $globalProduct->name)

    <div class="container mx-auto px-4 py-6">
        <x-breadcrumbs :items="[
            ['label' => 'Produkty', 'url' => route('products.index')],
            ...($globalProduct->category ? [['label' => $globalProduct->category->name, 'url' => route('products.show', $globalProduct->category)]] : []),
            ['label' => $globalProduct->name],
        ]"/>

        <x-product-info :product="$globalProduct" :lowestPrice="$lowestPrice"/>


{{-- https://www.penguinui.com/components/tabs --}}
        <div
            x-data="{
                tab: '{{ $globalProduct->tenantProducts->count() > 0 ? 'tenants' : ($bundles->isNotEmpty() ? 'bundles' : ($priceHistory->isNotEmpty() ? 'price' : 'parameters')) }}',
                highlightedTenant: null,
                init() {
                    // fragment: #tenant-{tenant_id}
                    const hash = window.location.hash.substring(1);
                    if (hash.startsWith('tenant-')) {
                        this.tab = 'tenants';
                        this.highlightedTenant = hash;
                    }
                }
            }"
            class="mt-10">

            <div class="text-sm font-medium text-center text-neutral-400 border-b border-neutral-800">
                <ul class="flex flex-wrap -mb-px">
                    @if($globalProduct->tenantProducts->count() > 0)
                        <x-tab name="'tenants'">
                            <x-slot name="additional">{{ $globalProduct->tenantProducts->count() }}</x-slot>
                            Kde kúpiť
                        </x-tab>
                    @endif

                    @if($priceHistory->isNotEmpty())
                        <x-tab name="'price'">
                            Cenový vývoj
                        </x-tab>
                    @endif

                    @if($bundles->isNotEmpty())
                        <x-tab name="'bundles'">
                            <x-slot name="additional">{{ $bundles->count() }}</x-slot>
                            Balíčky
                        </x-tab>
                    @endif

                    @if($groupedAttributes?->isNotEmpty())
                        <x-tab name="'parameters'">
                            Parametre
                        </x-tab>
                    @endif
                </ul>
            </div>

            @if($globalProduct->tenantProducts->count() > 0)
                <div x-show="tab === 'tenants'" x-transition class="mt-6 space-y-3">
                    @foreach($globalProduct->tenantProducts->sortBy(fn ($tp) => $lowestPricePerTenant[$tp->id] ?? PHP_INT_MAX) as $tenantProduct)
                        <x-tenant-product-accordion
                            :tenantProduct="$tenantProduct"
                            :lowestPrice="$lowestPricePerTenant[$tenantProduct->id] ?? null"
                            :isCheapest="$lowestPrice !== null && ($lowestPricePerTenant[$tenantProduct->id] ?? null) == $lowestPrice"
                            :categoryCoupons="$categoryCoupons->where('tenant_id', $tenantProduct->tenant_id)->values()"
                        />
                    @endforeach
                </div>
            @endif

            @if($priceHistory->isNotEmpty())
                <div x-show="tab === 'price'" x-transition class="mt-6"
                     x-init="$watch('tab', value => { if (value === 'price') $dispatch('init-price-chart') })">
                    <div class="bg-neutral-900 rounded-xl p-4 sm:p-6 h-96">
                        <canvas id="priceChart"
                                x-on:init-price-chart.window="$nextTick(() => initPriceChart())"
                                data-labels="{{ json_encode($priceHistory->pluck('week_label')) }}"
                                data-avg="{{ json_encode($priceHistory->pluck('avg_price')) }}"
                                data-min="{{ json_encode($priceHistory->pluck('min_price')) }}"
                                height="300"></canvas>
                    </div>
                </div>
            @endif

            @if($bundles->isNotEmpty())
                <div x-show="tab === 'bundles'" x-transition class="mt-6 space-y-4">
                    @foreach($bundles as $bundle)
                        <x-bundle-accordion :bundle="$bundle" />
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
