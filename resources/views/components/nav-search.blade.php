<div
    x-data="{ expanded: false }"
    @click.outside="expanded = false"
    @keydown.escape.window="expanded = false"
    class="flex items-center"
    x-transition
>
    <button
        x-show="!expanded"
        x-transition
        @click="expanded = true; $nextTick(() => $el.closest('[x-data]').querySelector('input')?.focus())"
        class="p-2 text-neutral-400 hover:text-white transition-colors rounded-md hover:bg-neutral-800"
    >
        <i class="fa-solid fa-magnifying-glass text-sm"></i>
    </button>

    <div
        x-show="expanded"
        x-transition
        x-cloak
        class="flex items-center gap-2"
    >
        <div class="w-80">
            <livewire:tenant.search-input key="desktop" />
        </div>
        <button @click="expanded = false" class="shrink-0 p-1.5 px-2 text-neutral-500 hover:text-white transition-colors rounded-md hover:bg-neutral-800">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
</div>
