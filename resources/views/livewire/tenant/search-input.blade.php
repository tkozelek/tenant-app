<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative w-full"
    x-transition
>
    <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-xs pointer-events-none"
           wire:loading.class="opacity-0" wire:target="search"></i>
        <i class="fa-solid fa-spinner animate-spin absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-xs pointer-events-none opacity-0"
           wire:loading.class.remove="opacity-0" wire:target="search"></i>

        <input
            wire:model.live.debounce.300ms="search"
            @focus="open = true"
            @input="open = true"
            @keydown.enter.prevent="if($el.value.length >= 2) window.location.href = '{{ route('products.search') }}?q=' + encodeURIComponent($el.value)"
            type="text"
            placeholder="Hľadať..."
            class="w-full pl-8 pr-3 py-2 bg-neutral-800 border border-neutral-700 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500 focus:border-neutral-500 transition-colors"
        >
    </div>

    @if(strlen($search) >= 2)
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition
            x-cloak
            class="absolute top-full mt-1.5 w-full bg-neutral-900 border border-neutral-800 rounded-lg overflow-hidden shadow-xl z-50"
        >
            <div wire:loading wire:target="search"
                 class="px-3 py-2.5 text-xs text-neutral-500">
                Hľadám...
            </div>

            <div wire:loading.remove wire:target="search">
                @if(isset($this->results) && $this->results->count() > 0)
                    @foreach($this->results as $result)
                        <a
                            href="{{ $result['url'] ?? '#' }}"
                            wire:key="result-{{ $loop->index }}"
                            class="flex items-center gap-3 px-3 py-2.5 hover:bg-neutral-800 transition-colors border-b border-neutral-800 last:border-0"
                        >

                        <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded
                            @if($result['type'] === 'tenant') bg-indigo-500/20
                            @elseif($result['type'] === 'category')
                            @else text-neutral-400
                            @endif">
                            {{ $result['label'] }}
                        </span>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-white truncate">{{ $result['name'] }}</p>
                                @if($result['sub'])
                                    <p class="text-xs text-neutral-500 truncate">{{ $result['sub'] }}</p>
                                @endif
                            </div>

                        </a>
                    @endforeach
                @else
                    <p class="px-3 py-2.5 text-xs text-neutral-500">Žiadne výsledky</p>
                @endif
            </div>
        </div>
    @endif
</div>
