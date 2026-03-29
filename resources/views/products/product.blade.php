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
                    @foreach($globalProduct->tenantProducts as $tenantProduct)
                        <x-tenant-product-accordion :tenantProduct="$tenantProduct" :lowestPrice="$lowestPricePerTenant[$tenantProduct->id] ?? null"/>
                    @endforeach
                </div>
            @endif

            @if($priceHistory->isNotEmpty())
                <div x-show="tab === 'price'" x-transition class="mt-6">
                    <div class="bg-neutral-900 rounded-xl p-4 sm:p-6 h-96">
                        <canvas id="priceChart" height="300"></canvas>
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
    @if($priceHistory->isNotEmpty())
        @push('styles')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        @endpush

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('priceChart');
                if (!ctx) return;

                ctx.height = 300;

                const labels = @json($priceHistory->pluck('week_label'));
                const avgPrices = @json($priceHistory->pluck('avg_price'));
                const minPrices = @json($priceHistory->pluck('min_price'));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Priemerná cena',
                                data: avgPrices,
                                borderColor: '#a78bfa',
                                backgroundColor: 'rgba(167, 139, 250, 0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 3,
                            },
                            {
                                label: 'Najnižšia cena',
                                data: minPrices,
                                borderColor: '#34d399',
                                backgroundColor: 'rgba(52, 211, 153, 0.1)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': ' + parseFloat(context.raw).toFixed(2) + ' €';
                                    },
                                },
                            },
                            legend: {
                                labels: {
                                    color: '#d4d4d4',
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {color: '#a3a3a3'},
                                grid: {color: 'rgba(115, 115, 115, 0.2)'},
                            },
                            y: {
                                ticks: {
                                    color: '#a3a3a3',
                                    callback: function (value) {
                                        return value.toFixed(2) + ' €';
                                    },
                                },
                                grid: {color: 'rgba(115, 115, 115, 0.2)'},
                            },
                        },
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
