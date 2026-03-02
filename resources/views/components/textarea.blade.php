<div {{ $attributes->merge(['class' => '']) }}>
    <x-input-label :for="$name" :value="$label" />

    <textarea
        @disabled($disabled)
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition duration-150 ease-in-out"
        placeholder="{{ $placeholder }}"
        {{ $req ? 'required' : '' }}
    >{{ $value }}</textarea>

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
