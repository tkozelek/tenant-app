<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-sm"></i>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Hladať podla názvu balíka..."
                class="w-full pl-9 pr-4 py-2.5 bg-neutral-900 border border-neutral-700 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-neutral-500 transition-colors"
            />
        </div>

        @if($this->tenants->count() > 1)
            <x-select
                wire:model.live="selectedTenant"
                name="selectedTenant"
                placeholder="Vsetci"
                :options="$this->tenants->pluck('name', 'id')->toArray()"
                :value="$selectedTenant"
            />
        @endif
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none" class="transition-opacity duration-200">
        @if($this->bundles->isEmpty())
            <div class="text-center py-24 text-neutral-500">
                <i class="fa-solid fa-box-open text-4xl mb-4 block"></i>
                Nenašli sa žiadne balíky.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($this->bundles as $bundle)
                    <x-bundle-card wire:key="bundle-{{ $bundle->id }}" :bundle="$bundle" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $this->bundles->links() }}
            </div>
        @endif
    </div>
</div>
