@props(['items'])

<nav class="flex items-center gap-2 text-sm text-neutral-500 mb-6">
    @foreach($items as $item)
        @if(!$loop->first)
            <i class="fa-solid fa-chevron-right text-sm text-neutral-700"></i>
        @endif

        @if(empty($item['url']))
            <span class="text-white font-bold">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" class="hover:text-white transition-colors">{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
