@props(['active' => false])

@php
    $baseClasses = 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out';

    $activeClasses = 'border-indigo-400 text-indigo-800 bg-indigo-50
                      focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700
                      dark:text-gray-200 dark:bg-gray-600 dark:focus:bg-gray-500 dark:focus:text-gray-200';

    $inactiveClasses = 'border-transparent text-gray-600
                        hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300
                        focus:text-gray-900 focus:bg-gray-50 focus:border-gray-300
                        dark:text-gray-200 dark:hover:text-gray-200 dark:hover:bg-gray-600
                        dark:focus:bg-gray-500 dark:focus:text-gray-200';

    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
