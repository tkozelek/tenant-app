<nav x-data="{ open: false, mobileSearch: false }" class="bg-white dark:bg-neutral-950 sticky top-0 z-50 border-b border-neutral-200 dark:border-neutral-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <x-application-logo class="h-8 w-auto fill-current text-neutral-900 dark:text-white" />
                    </a>
                </div>

                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        Produkty
                    </x-nav-link>
                    <x-nav-link :href="route('bundles.index')" :active="request()->routeIs('bundles.*')">
                        Balíky
                    </x-nav-link>
                    <x-nav-link :href="route('coupons.index')" :active="request()->routeIs('coupons.*')">
                        Kupony
                    </x-nav-link>
                    <x-nav-link :href="route('price.compare')" :active="request()->routeIs('price.compare')">
                        Porovnanie
                    </x-nav-link>
                    @auth()
                        @include('layouts.partials.navlinks')
                    @endauth
                </div>
            </div>

            <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end px-2">
                <x-nav-search />
            </div>

            <div class="hidden lg:flex lg:items-center lg:ms-6 space-x-4">
                @auth()
                    @include('layouts.partials.desktop.desktop')
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white transition-colors">
                            Prihlásenie
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 bg-neutral-900 hover:bg-neutral-800 dark:bg-white dark:hover:bg-neutral-200 text-white dark:text-neutral-900 text-sm font-medium rounded-md transition-colors shadow-sm">
                            Registrácia
                        </a>
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center gap-1 lg:hidden">
                <button @click="mobileSearch = true" class="inline-flex items-center justify-center p-2 rounded-md text-neutral-400 hover:text-neutral-500 hover:bg-neutral-100 dark:text-neutral-500 dark:hover:text-neutral-400 dark:hover:bg-neutral-800 focus:outline-none transition duration-150 ease-in-out">
                    <i class="fa-solid fa-magnifying-glass h-5 w-5 text-base"></i>
                </button>
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-neutral-400 hover:text-white hover:bg-neutral-800 focus:outline-none transition"
                >
                    <span class="w-5 h-5 flex items-center justify-center">
                        <i x-show="!open" class="fa-solid fa-bars"></i>
                        <i x-show="open" x-cloak class="fa-solid fa-x"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                Produkty
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bundles.index')" :active="request()->routeIs('bundles.*')">
                Balíky
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('coupons.index')" :active="request()->routeIs('coupons.*')">
                Kupony
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('price.compare')" :active="request()->routeIs('price.compare')">
                Porovnanie
            </x-responsive-nav-link>
            @auth()
                @include('layouts.partials.phone.phone')
            @else
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    Prihlásenie
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                    Registrácia
                </x-responsive-nav-link>
            @endauth
        </div>
    </div>

    <div
        x-show="mobileSearch"
        @keydown.escape.window="mobileSearch = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-[100] bg-neutral-950 lg:hidden flex flex-col"
    >
        <div class="flex items-center gap-3 px-4 h-16 border-b border-neutral-800 shrink-0">
            <div class="flex-1">
                <livewire:tenant.search-input key="mobile" />
            </div>
            <button
                @click="mobileSearch = false"
                class="shrink-0 p-2 text-neutral-400 hover:text-white transition-colors"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</nav>
