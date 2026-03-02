<div {{ $attributes->merge(['class' => '']) }}>
    <x-input-label :for="$name" :value="$label" />

    <select
        @disabled($disabled)
        id="{{ $name }}"
        name="{{ $name }}"
        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition duration-150 ease-in-out"
        {{ $req ? 'required' : '' }}
    >
        @if($placeholder)
            <option value="" disabled {{ is_null($value) ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $labelOption)
            <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>
                {{ $labelOption }}
            </option>
        @endforeach
    </select>

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
