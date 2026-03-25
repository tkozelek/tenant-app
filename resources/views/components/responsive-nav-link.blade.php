@props(['active' => false])

@php
    $baseClasses = 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out';

    $activeClasses = 'border-indigo-400 text-indigo-800 bg-indigo-50
                      focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700
                      dark:text-neutral-200 dark:bg-neutral-600 dark:focus:bg-neutral-500 dark:focus:text-neutral-200';

    $inactiveClasses = 'border-transparent text-neutral-600
                        hover:text-neutral-900 hover:bg-neutral-50 hover:border-neutral-300
                        focus:text-neutral-900 focus:bg-neutral-50 focus:border-neutral-300
                        dark:text-neutral-200 dark:hover:text-neutral-200 dark:hover:bg-neutral-600
                        dark:focus:bg-neutral-500 dark:focus:text-neutral-200';

    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
