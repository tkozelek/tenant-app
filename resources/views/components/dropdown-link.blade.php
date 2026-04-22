@props(['isButton' => false])
@php
    $classes = 'block w-full px-4 py-2 text-start text-sm leading-5 text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:bg-neutral-800 hover:text-neutral-700 dark:hover:bg-neutral-700 dark:hover:text-neutral-200 focus:outline-none focus:bg-neutral-100 dark:focus:bg-neutral-700 transition duration-150 ease-in-out';
@endphp
@unless($isButton)
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endunless
