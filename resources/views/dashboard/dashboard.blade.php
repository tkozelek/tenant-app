<x-app-layout>
    @section('title', $title)
    <div class="py-10 sm:py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-xl rounded-xl p-6 sm:p-8">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b border-gray-200 dark:border-neutral-700">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-white">
                            Moje Obchody
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
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

                @isset($tenants)
                    @if($tenants->isNotEmpty())
                        <section class="mb-10">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($tenants as $tenant)
                                    <x-tenant-card :tenant="$tenant" :user="auth()->user()" />
                                @endforeach
                            </div>
                        </section>
                    @else
                        <div class="text-center py-10 sm:py-16">
                            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5A2.25 2.25 0 0 0 11.25 11.25H4.5A2.25 2.25 0 0 0 2.25 13.5V21M4.5 11.25h6.75M18.75 11.25c0-1.036-.84-1.875-1.875-1.875A1.875 1.875 0 0 0 15 11.25c0 1.036.84 1.875 1.875 1.875h1.875m-1.875 0h1.875c1.036 0 1.875-.84 1.875-1.875S17.825 7.5 16.875 7.5A1.875 1.875 0 0 0 15 9.375m0 0A1.875 1.875 0 0 0 13.125 7.5S12 7.5 12 9.375m0 0h7.5" />
                            </svg>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Žiadne obchody na zobrazenie</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Začnite tým, že si vytvoríte svoj prvý obchod alebo požiadate o prístup k existujúcemu.</p>
                            <div class="mt-6">
                                <a href="{{ route('tenant.create') }}">
                                    <x-primary-button class="!text-base !font-semibold !px-5 !py-2.5 flex items-center">
                                        <i class="fa-solid fa-plus mr-2 opacity-80"></i>Vytvoriť prvý obchod
                                    </x-primary-button>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Neboli nájdené žiadne informácie o obchodoch. Skúste prosím načítať stránku znova.</p>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</x-app-layout>
