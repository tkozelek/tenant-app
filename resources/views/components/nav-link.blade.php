@props(['active'])

@php
$generalClasses = 'inline-flex items-center px-4 py-3 rounded-md text-sm font-medium leading-5';
$classes = ($active ?? false)
            ? ' text-neutral-900 bg-neutral-300 dark:text-white dark:bg-neutral-800 focus:outline-none transition duration-150 ease-in-out'
            : ' text-neutral-500 hover:text-neutral-700 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 focus:outline-none focus:text-neutral-200 focus:bg-neutral-600 transition duration-150 ease-in-out';

$classes = $generalClasses . $classes;

@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
