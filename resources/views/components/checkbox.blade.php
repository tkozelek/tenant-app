<div class="flex items-start">
    <div class="flex items-center h-5">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="checkbox"
            value="{{ $value }}"
            @checked($checked)
            @disabled($disabled)
            class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white/50 dark:bg-gray-800/50 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-0 transition-all duration-200 ease-in-out cursor-pointer"
        >
    </div>
    <div class="ml-3 text-sm leading-5">
        <label for="{{ $name }}" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
            {{ $label }}
        </label>
        <x-input-error :messages="$errors->get($name)" class="mt-1" />
    </div>
</div>
