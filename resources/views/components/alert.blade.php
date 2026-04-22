@php
    $types = [
        'info' => 'bg-blue-50 text-blue-800 dark:bg-gray-800 dark:text-blue-400 border-blue-300 dark:border-blue-800',
        'success' => 'bg-green-50 text-green-800 dark:bg-gray-800 dark:text-green-400 border-green-300 dark:border-green-800',
        'warning' => 'bg-yellow-50 text-yellow-800 dark:bg-gray-800 dark:text-yellow-300 border-yellow-300 dark:border-yellow-800',
        'error' => 'bg-red-50 text-red-800 dark:bg-gray-800 dark:text-red-400 border-red-300 dark:border-red-800',
    ];

    $typeClasses = $types[$type] ?? $types['info'];
@endphp

<div {{ $attributes->merge(['class' => "p-4 border rounded-lg {$typeClasses}"]) }} role="alert">
    <div class="flex items-center">
        @if($title)
            <h3 class="text-lg font-medium">{{ $title }}</h3>
        @endif
    </div>
    <div class="{{ $title ? 'mt-2' : '' }} text-sm">
        {{ $slot }}
    </div>
</div>
