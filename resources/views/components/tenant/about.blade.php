@props(['tenant'])

<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl p-8 transition-colors shadow-sm dark:shadow-none">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-600 dark:text-neutral-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-neutral-900 dark:text-white">About Us</h2>
    </div>

    <div class="prose prose-neutral dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-300 leading-relaxed">
        @if(!empty($tenant->description))
            {!! $tenant->description !!}
        @else
            <div class="flex flex-col items-center justify-center py-12 px-6 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700">
                <p class="text-neutral-500 dark:text-neutral-400 italic font-medium text-center">
                    We're currently crafting our store's story. Check back soon!
                </p>
            </div>
        @endif
    </div>
</div>
