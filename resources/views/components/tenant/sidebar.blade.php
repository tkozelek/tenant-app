@props(['tenant'])

<div class="lg:col-span-3">
    <div class="sticky top-24">
        <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-lg dark:shadow-none transition-colors">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 relative">

                <div class="w-28 h-28 -mt-16 mb-4 rounded-xl overflow-hidden bg-white dark:bg-neutral-800 border-4 border-white dark:border-neutral-900 shadow-sm relative z-50">
                    @if ($tenant->hasMedia('images'))
                        <img src="{{ $tenant->getImageUrl() }}" alt="{{ $tenant->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 text-3xl font-bold">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white tracking-tight">
                    {{ $tenant->name }}
                </h1>

                @if(!empty($tenant->short_description))
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                        {{ $tenant->short_description }}
                    </p>
                @endif

                <div class="flex items-center gap-2 mt-5">
                    @foreach(['facebook-f', 'instagram', 'twitter'] as $social)
                        <a href="#" class="w-9 h-9 flex items-center justify-center rounded-lg bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white transition-colors">
                            <i class="fa-brands fa-{{ $social }} text-sm"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="p-6 space-y-3 bg-neutral-50/50 dark:bg-neutral-800/20">
                <a href="https://google.com" target="_blank"
                   class="flex items-center justify-center w-full gap-2 px-4 py-3 bg-neutral-900 hover:bg-neutral-800 dark:bg-white dark:hover:bg-neutral-200 text-white dark:text-neutral-900 font-medium rounded-lg transition-colors shadow-md dark:shadow-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Visit Website</span>
                </a>

                <button class="flex items-center justify-center w-full gap-2 px-4 py-3 bg-white hover:bg-neutral-50 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700 font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Contact Store</span>
                </button>
            </div>

            <div class="grid grid-cols-3 divide-x divide-neutral-200 dark:divide-neutral-800 border-t border-neutral-200 dark:border-neutral-800">
                <div class="p-4 text-center">
                    <span class="block text-base font-bold text-neutral-900 dark:text-white">4.9</span>
                    <span class="text-[10px] uppercase font-bold text-neutral-400 dark:text-neutral-500 tracking-wider">Rating</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-base font-bold text-neutral-900 dark:text-white">2h</span>
                    <span class="text-[10px] uppercase font-bold text-neutral-400 dark:text-neutral-500 tracking-wider">Reply</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-base font-bold text-neutral-900 dark:text-white">Yes</span>
                    <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-500 tracking-wider flex items-center justify-center gap-1">
                        Verified <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
