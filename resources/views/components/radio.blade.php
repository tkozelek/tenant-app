<div {{ $attributes->merge(['class' => '']) }}>
    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $label }}</span>

    <div class="space-y-2">
        @foreach($options as $optionValue => $optionLabel)
            <label class="inline-flex items-center">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $optionValue }}"
                    @checked($value == $optionValue)
                    @disabled($disabled)
                    class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out"
                >
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $optionLabel }}</span>
            </label>
        @endforeach
    </div>

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
