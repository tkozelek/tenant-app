@props(['url' => null, 'alt' => '', 'icon' => 'fa-image', 'cover' => true])

<div {{ $attributes->merge(['class' => 'bg-neutral-800 border border-neutral-700 overflow-hidden shrink-0 flex items-center justify-center']) }}>
    @if($url)
        <img src="{{ $url }}" alt="{{ $alt }}" class="w-full h-full {{ $cover ? 'object-cover' : 'object-contain' }}">
    @else
        <i class="fa-solid {{ $icon }} text-neutral-600 text-xs"></i>
    @endif
</div>
