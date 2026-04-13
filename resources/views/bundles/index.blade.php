<x-app-layout>
    @section('title', 'Balíky')

    <div class="container mx-auto px-4 py-8 sm:py-12">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white">Balíky</h1>
                <p class="text-neutral-500 text-sm mt-1">Zvýhodnené sety produktov od našich predajcov.</p>
            </div>
        </div>

        <livewire:bundle-browser />
    </div>
</x-app-layout>
