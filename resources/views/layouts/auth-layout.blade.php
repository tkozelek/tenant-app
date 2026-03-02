<x-guest-layout>
    <div class="h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- https://tailkits.com/components/tailwind-background-snippets/ -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 -z-10 h-full w-full items-center px-5 py-24 [background:radial-gradient(125%_125%_at_50%_10%,#000_40%,#63e_100%)]"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative">
            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-2xl py-10 px-8 shadow-2xl shadow-indigo-200/40 dark:shadow-none border border-white/50 dark:border-gray-800/50 sm:rounded-[2.5rem] sm:px-12 transition-all duration-300">
                {{ $slot }}
            </div>
        </div>

    </div>
</x-guest-layout>
