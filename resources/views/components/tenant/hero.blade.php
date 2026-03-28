@props(['tenant'])

<div class="relative h-52 md:h-64 w-full overflow-hidden">
    @if ($tenant->hasMedia('titles'))
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ $tenant->getTitleImageUrl() }}');">
        </div>
    @else
        <div class="absolute inset-0 bg-neutral-900">
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;">
            </div>
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

    <div class="absolute top-4 left-4 z-20">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-white text-sm hover:bg-white/20 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m0 0l7 7m-7-7l7-7"/>
            </svg>
            Späť
        </a>
    </div>

    <div class="absolute bottom-0 left-0 right-0 px-6 pb-5 flex items-end gap-4">
        <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 border border-white/20 shadow-lg">
            @if ($tenant->hasMedia('images'))
                <img src="{{ $tenant->getImageUrl() }}" alt="{{ $tenant->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-neutral-800 text-white text-xl font-bold">
                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-xl font-bold text-white leading-tight">{{ $tenant->name }}</h1>
            @if(!empty($tenant->short_description))
                <p class="text-sm text-white/60 mt-0.5">{{ $tenant->short_description }}</p>
            @endif
        </div>
    </div>
</div>
