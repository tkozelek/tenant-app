<x-app-layout>
    @section('title', 'Balíky')

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white">Balíky</h1>
                <p class="text-neutral-500 text-sm mt-1">Zvýhodnené sety produktov od našich predajcov.</p>
            </div>
            <span class="text-sm text-neutral-500">{{ $bundles->total() }} balíkov</span>
        </div>

        @if($bundles->isEmpty())
            <div class="text-center py-24 text-neutral-500">
                <i class="fa-solid fa-box-open text-4xl mb-4 block"></i>
                Momentálne nie sú dostupné žiadne balíky.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($bundles as $bundle)
                    <x-bundle-card :bundle="$bundle" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $bundles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
