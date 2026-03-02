@props(['title', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="flex items-center justify-between w-full px-4 py-3 mt-2 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ $active ? 'bg-gray-800 text-white' : '' }}">
        <div class="flex items-center">
            @if (isset($icon))
                <span class="w-5 h-5 mr-3">{{ $icon }}</span>
            @endif
            <span class="font-medium">{{ $title }}</span>
        </div>

        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="pl-4 mt-2 space-y-1"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
