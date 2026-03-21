<section class="py-24 bg-white dark:bg-neutral-950">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">

            <h2 class="text-2xl md:text-4xl font-bold text-neutral-900 dark:text-white mb-4">
                Pripravení začať?
            </h2>
            <p class="text-neutral-500 dark:text-neutral-400 mb-10">
                Vytvorte si účet a nastavte prvý obchod za pár minút.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-neutral-900 hover:bg-neutral-800 dark:bg-white dark:hover:bg-neutral-200 text-white dark:text-neutral-900 text-sm font-semibold rounded-md transition-colors shadow-sm">
                    Vytvoriť účet
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border border-neutral-200 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-500 text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white text-sm font-medium rounded-md transition-colors">
                    Prihlásiť sa
                </a>
            </div>

        </div>
    </div>
</section>
