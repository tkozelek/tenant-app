@props(['name'])

<li class="me-2">
    <button href="#"
            x-on:click="tab = {{ $name }}"
            x-bind:class="tab === {{ $name }}
                                       ? 'text-white border-b border-white'
                                       : 'border-b border-transparent hover:text-neutral-200 hover:border-neutral-400'"
            class="inline-block p-4 rounded-t-lg transition-colors"
    >
        {{ $slot }}
        @isset($additional)
            <span class="ml-1.5 text-xs bg-neutral-800 text-neutral-400 px-2 py-0.5 rounded-full">
                {{ $additional }}
            </span>
        @endisset
    </button>
</li>
