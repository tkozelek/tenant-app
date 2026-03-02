@props(['isButton' => false])
@php
    $classes = 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out';
@endphp
@unless($isButton)
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endunless
