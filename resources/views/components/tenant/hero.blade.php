@props(['tenant'])

<div class="relative h-52 md:h-64 w-full overflow-hidden">
    @if ($tenant->hasMedia('titles'))
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ $tenant->getTitleImageUrl() }}');">
        </div>
    @else
        <div class="absolute inset-0 bg-neutral-900">
            <div class="absolute inset-0 bg-linear-to-r from-zinc-500 via-stone-600 to-zinc-900"></div>
        </div>
    @endif

    <div class="absolute inset-0 bg-linear-to-t from-black/75 via-black/20 to-transparent"></div>

    <div class="container mx-auto absolute top-4 left-0 right-0 z-20 px-6 flex justify-between items-start">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-neutral-900 border border-neutral-700 rounded-lg text-white text-sm hover:bg-neutral-800 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Späť
        </a>

        @auth
            @can('view', $tenant)
                <a href="{{ route('filament.tenant.pages.dashboard', $tenant) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-neutral-900 border border-neutral-700 rounded-lg text-white text-sm hover:bg-neutral-800 transition-colors">
                    <i class="fa-solid fa-gear"></i>
                    Panel
                </a>
            @endcan
        @endauth
    </div>

    <div class="container mx-auto absolute bottom-0 left-0 right-0 px-6 pb-5 flex items-end gap-4">
        <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 border border-white/20 shadow-lg">
            @if ($tenant->hasMedia('images'))
                <img src="{{ $tenant->getImageUrl() }}" alt="{{ $tenant->name }}"
                     class="w-full h-full object-cover">
            @else
                <div
                    class="w-full h-full flex items-center justify-center bg-neutral-800 text-white text-xl font-bold">
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
