@props(['tenant'])

<div class="relative h-64 md:h-80 w-full overflow-hidden group">
    @if ($tenant->hasMedia('titles'))
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
             style="background-image: url('{{ $tenant->getTitleImageUrl() }}');">
        </div>
    @else
        <div class="absolute inset-0 bg-neutral-100 dark:bg-neutral-900">
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]"
                 style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-neutral-200 via-neutral-100 to-white dark:from-neutral-800 dark:via-neutral-900 dark:to-neutral-950 opacity-60"></div>
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-60"></div>

    <div class="top-5 left-5 absolute z-20">
        <a href="{{ auth()->user() ? route('dashboard.index') : url('/') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 dark:bg-neutral-900/80 backdrop-blur-md border border-white/20 dark:border-neutral-700 rounded-full text-neutral-700 dark:text-neutral-200 text-sm font-medium hover:bg-white dark:hover:bg-neutral-800 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 12H5m0 0l7 7m-7-7l7-7"/>
            </svg>
            <span>Back</span>
        </a>
    </div>
</div>
