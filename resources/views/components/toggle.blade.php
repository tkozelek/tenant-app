<div {{ $attributes->merge(['class' => '']) }}>
    <label class="inline-flex items-center cursor-pointer">
        <div class="relative">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                @checked($checked)
                @disabled($disabled)
                class="sr-only peer"
            >
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
        </div>
        <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
    </label>

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
