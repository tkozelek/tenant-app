<x-layouts.admin>
@section('meta.description', $tenant->short_description ?? null)
    @section('og.title', $tenant->name ?? null)
    @section('og.description', $tenant->short_description ?? null)
    @section('og.url', request()->url() ?? null)

    <div class="min-h-screen bg-white dark:bg-neutral-950 transition-colors duration-300">
        <x-tenant.hero :tenant="$tenant" />

        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 relative z-10 -mt-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <x-tenant.sidebar :tenant="$tenant" />

                <div class="lg:col-span-9 flex flex-col gap-8 pt-4 lg:pt-0">

                    <x-tenant.about :tenant="$tenant" />

                    <x-tenant.product-grid>
                        @foreach(range(1, 6) as $product)
                            <x-tenant.product-card :product="$product" />
                        @endforeach
                    </x-tenant.product-grid>

                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
