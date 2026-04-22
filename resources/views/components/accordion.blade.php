@props(['open' => 'false'])

<div x-data="{ open: {{ $open }} }"
     {{ $attributes->merge(['class' => 'bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden transition-all']) }}>

    <button @click="open = !open"
            class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-neutral-800 transition-colors">
        <div class="flex items-center gap-4">
            {{ $header }}
        </div>
        <div class="flex items-center gap-4 shrink-0 ml-4">
            {{ $aside ?? '' }}
            <i class="fa-solid fa-chevron-down text-neutral-500 transition-transform duration-200"
               :class="open && 'rotate-180'"></i>
        </div>
    </button>

    <div x-show="open" x-transition class="border-t border-neutral-800">
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    </div>
</div>
