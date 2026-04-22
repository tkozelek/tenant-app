@props(['tenant'])

<div
    class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-md flex flex-col h-full group relative overflow-hidden">

    <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
        @if($tenant->hasMedia('images'))
            <img
                loading="lazy"
                class="w-full h-full object-cover"
                src="{{ $tenant->getFirstMediaUrl('images') }}"
                alt="{{ $tenant->name }}"
            />
        @else
            <div
                class="w-full h-full flex items-center justify-center bg-neutral-200 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 text-4xl font-semibold">
                {{ strtoupper(Str::substr($tenant->name, 0, 2)) }}
            </div>
        @endif
    </div>

    @include('tenant.partials._overimage-content')

    <div class="p-5 flex flex-col flex-grow">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate" title="{{ $tenant->name }}">
                {{ $tenant->name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1 h-10 leading-relaxed">
                {{ $tenant->short_description ?? 'Žiadny popis.' }}
            </p>
        </div>

        <div class="mt-auto space-y-3">
            <div
                class="flex items-center text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-neutral-800 px-2 py-1.5 border border-gray-200 dark:border-neutral-700 rounded">
                <i class="fa-solid fa-globe mr-2 text-gray-400"></i>
                <span class="truncate font-mono select-all">{{ $tenant->slug }}</span>
            </div>

            <a href="{{ route('filament.tenant.pages.dashboard', ['tenant' => $tenant->slug]) }}"
               class="w-full flex justify-center items-center py-2 px-4 text-sm font-medium text-white bg-neutral-700 hover:bg-neutral-600 dark:bg-neutral-700 dark:hover:bg-neutral-600 border border-neutral-600 rounded focus:outline-none focus:ring-1 focus:ring-neutral-500 transition-colors">
                Spravovať obchod
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>
