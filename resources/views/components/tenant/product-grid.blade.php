<div>
    <div class="flex items-center justify-between mb-6 px-1">
        <h2 class="text-xl font-bold text-neutral-900 dark:text-white flex items-center gap-3">
            <span class="w-1.5 h-6 bg-neutral-900 dark:bg-white rounded-full inline-block"></span>
            Latest Offers
        </h2>
        <a href="#" class="group text-sm font-semibold text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white flex items-center gap-1 transition-colors">
            View All
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        {{ $slot }}
    </div>
</div>
