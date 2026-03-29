<div x-data="{ open: true, search: '' }">
    @if($label)
        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
            <h4 class="text-sm font-semibold text-white">{{ $label }}</h4>
            <i class="fa-solid fa-chevron-down text-neutral-500 text-xs transition-transform duration-200" :class="open && 'rotate-180'"></i>
        </button>
    @endif

    <div x-show="open" x-collapse class="mt-3">
        <input type="text"
               x-model="search"
               placeholder="{{ $placeholder }}"
               class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-3 py-2 mb-2 focus:outline-none focus:border-neutral-500">

        <div class="max-h-48 overflow-y-auto space-y-1">
            @foreach($options as $option)
                <label x-show="!search || '{{ strtolower($option->name) }}'.includes(search.toLowerCase())"
                       class="group flex items-center gap-2.5 cursor-pointer px-1 py-1 rounded hover:bg-neutral-800/50">
                    <input type="checkbox"
                           wire:click.debounce.300ms="{{ $wireMethod }}({{ $option->id }})"
                           @checked(in_array($option->id, $selected))
                           class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-white focus:ring-neutral-500 focus:ring-offset-0 cursor-pointer">
                    <span class="text-sm text-neutral-400 group-hover:text-white transition-colors" x-text="'{{ $option->name }}'"></span>
                </label>
            @endforeach
        </div>
    </div>
</div>
