@props(['tenant', 'options' => false])

<div
    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out flex flex-col h-full group relative">

    <div class="relative h-48 w-full overflow-hidden rounded-t-xl bg-gray-100 dark:bg-gray-700">
        @if($tenant->hasMedia('images'))
            <img
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                src="{{ $tenant->getFirstMediaUrl('images') }}"
                alt="{{ $tenant->name }}"
            />
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        @else
            <div
                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-4xl font-bold shadow-inner">
                {{ strtoupper(Str::substr($tenant->name, 0, 2)) }}
            </div>
        @endif
    </div>

    @include('tenant.partials._overimage-content')

    <div class="p-5 flex flex-col flex-grow">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $tenant->name }}">
                {{ $tenant->name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1 h-10 leading-relaxed">
                {{ $tenant->short_description ?? 'Žiadny popis.' }}
            </p>
        </div>

        <div class="mt-auto space-y-4">
            <div
                class="flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 rounded-md p-2 border border-gray-100 dark:border-gray-700/50">
                <i class="fa-solid fa-globe mr-2 text-gray-400"></i>
                <span class="truncate font-mono select-all">{{ $tenant->slug }}</span>
            </div>

            <a href="{{ route('tenant.show', $tenant) }}"
               class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-all duration-200 shadow-md hover:shadow-lg overflow-hidden">
                <span class="relative z-10 flex items-center">
                    Spravovať obchod
                    <i class="fa-solid fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </span>
                <div
                    class="absolute inset-0 h-full w-full scale-0 rounded-lg transition-all duration-300 group-hover:scale-100 group-hover:bg-indigo-700/50"></div>
            </a>
        </div>
    </div>
</div>
