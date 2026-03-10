@props(['name', 'label' => null, 'value' => ''])

<div class="mt-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <textarea id="{{ $name }}" name="{{ $name }}">{{ $value }}</textarea>

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
