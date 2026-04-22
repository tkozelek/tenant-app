@props(['active' => false])

@php
    $classes = $active
                ? 'flex items-center px-4 py-3 mt-2 text-white bg-gray-800 rounded-lg transition-colors'
                : 'flex items-center px-4 py-3 mt-2 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="w-5 h-5 mr-3">{{ $icon }}</span>
    @else
        <span class="w-5 mr-3 border-b-2 border-gray-700 opacity-50"></span>
    @endif

    <span class="font-medium">{{ $slot }}</span>
</a>
