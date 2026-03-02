<nav x-data="{ open: false }" class="bg-white/80 dark:bg-neutral-950/80 backdrop-blur-md sticky top-0 z-50 border-b border-neutral-200 dark:border-neutral-800 transition-colors duration-300">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <x-application-logo class="h-8 w-auto fill-current text-neutral-900 dark:text-white" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth()
                        @include('layouts.partials.navlinks')
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
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

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-neutral-400 hover:text-neutral-500 hover:bg-neutral-100 dark:text-neutral-500 dark:hover:text-neutral-400 dark:hover:bg-neutral-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950">
        <div class="pt-2 pb-3 space-y-1">
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
</nav>
