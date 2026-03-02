@props(['active'])

@php
$generalClasses = 'inline-flex items-center px-4 py-3 rounded-md text-sm font-medium leading-5';
$classes = ($active ?? false)
            ? ' text-gray-900 bg-gray-300 dark:text-white dark:bg-gray-800 focus:outline-none transition duration-150 ease-in-out'
            : ' text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none focus:text-gray-200 focus:bg-gray-600 transition duration-150 ease-in-out';

$classes = $generalClasses . $classes;

@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
