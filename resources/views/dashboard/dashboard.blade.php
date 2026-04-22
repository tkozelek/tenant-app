<x-app-layout>
    @section('title', $title)
    <div class="py-10 sm:py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-neutral-800 border border-neutral-700 rounded-md p-6 sm:p-8">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b border-neutral-700">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">
                            Moje obchody
                        </h1>
                        <p class="mt-1 text-sm text-neutral-400">
                            Prehľad vašich spravovaných a priradených obchodov.
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 shrink-0">
                        <a href="{{ route('tenant.create') }}">
                            <x-primary-button class="!text-base !font-semibold !px-5 !py-2.5 flex items-center">
                                <i class="fa-solid fa-plus mr-2 opacity-80"></i>Vytvoriť obchod
                            </x-primary-button>
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ request()->url() }}" class="mb-6">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Hľadať obchod..."
                        class="w-full sm:w-80 bg-neutral-700 border border-neutral-600 text-white text-sm rounded px-4 py-2 focus:outline-none focus:border-neutral-500 placeholder:text-neutral-400"
                    >
                </form>

                @if($tenants->isNotEmpty())
                    <section class="mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($tenants as $tenant)
                                <x-tenant-card :tenant="$tenant" :user="auth()->user()" />
                            @endforeach
                        </div>
                    </section>

                    <div>
                        {{ $tenants->links() }}
                    </div>
                @else
                    <div class="text-center py-10 sm:py-16">
                        <i class="fa-solid fa-store text-5xl text-neutral-600"></i>
                        @if($search)
                            <h3 class="mt-4 text-xl font-semibold text-white">Žiadne výsledky pre „{{ $search }}"</h3>
                            <p class="mt-2 text-sm text-neutral-400">Skúste iný výraz.</p>
                        @else
                            <h3 class="mt-4 text-xl font-semibold text-white">Žiadne obchody na zobrazenie</h3>
                            <p class="mt-2 text-sm text-neutral-400">Začnite tým, že si vytvoríte svoj prvý obchod alebo požiadate o prístup k existujúcemu.</p>
                            <div class="mt-6">
                                <a href="{{ route('tenant.create') }}">
                                    <x-primary-button class="!text-base !font-semibold !px-5 !py-2.5 flex items-center">
                                        <i class="fa-solid fa-plus mr-2 opacity-80"></i>Vytvoriť prvý obchod
                                    </x-primary-button>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
