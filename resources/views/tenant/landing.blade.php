<x-app-layout>
@section('meta.description', $tenant->short_description ?? null)
    @section('og.title', $tenant->name ?? null)
    @section('og.description', $tenant->short_description ?? null)
    @section('og.url', request()->url() ?? null)

    <div class="min-h-screen bg-neutral-950">
        <x-tenant.hero :tenant="$tenant" />

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col gap-10">
            @if(!empty($tenant->description))
                <x-tenant.about :tenant="$tenant" />
            @endif

            @if($products->count())
                <x-tenant.product-grid>
                    @foreach($products as $product)
                        <x-tenant.product-card :product="$product" />
                    @endforeach
                </x-tenant.product-grid>
            @endif
        </div>
    </div>
</x-app-layout>
