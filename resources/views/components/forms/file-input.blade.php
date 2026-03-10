<div class="p-4 border border-gray-200 dark:border-neutral-700 rounded-lg bg-gray-50 dark:bg-neutral-700/30">
    <x-input-label :for="$name" :value="$label" class="mb-2" />

    @if($currentImage)
        <div class="mb-4 relative group w-full max-w-xs h-32">
            <img src="{{ $currentImage }}" alt="{{ $label }}" class="w-full h-full object-cover rounded-lg shadow-sm border border-gray-200 dark:border-neutral-600">

            @if($deleteAction)
                <button type="button"
                        onclick="if(confirm('Naozaj zmazať tento obrázok?')) document.getElementById('{{ $deleteAction }}').submit();"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-md hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            @endif
        </div>
    @endif

    <input id="{{ $name }}" name="{{ $name }}" type="file" accept="{{ $accept }}" class="block w-full text-sm text-gray-500 dark:text-gray-400
        file:mr-4 file:py-2 file:px-4
        file:rounded-full file:border-0
        file:text-sm file:font-semibold
        file:bg-indigo-50 file:text-indigo-700
        hover:file:bg-indigo-100
        dark:file:bg-indigo-900/50 dark:file:text-indigo-300
        dark:hover:file:bg-indigo-800
        cursor-pointer file:cursor-pointer
        file:transition-colors file:duration-200
    "/>

    @if($helpText)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $helpText }}</p>
    @endif

    <x-input-error class="mt-2" :messages="$errors->get($name)" />
</div>
