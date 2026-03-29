@if($this->tenants->count() > 1)
    <div class="border-b border-neutral-800 pb-5">
        <x-searchable-select
            :options="$this->tenants"
            :selected="$selectedTenants"
            label="Predajca"
            wire-method="toggleTenant"
        />
    </div>
@endif

@if(!empty($priceRange) && $priceRange['min'] < $priceRange['max'])
    @php $priceStep = ($priceRange['max'] - $priceRange['min']) > 10 ? 1 : 0.1; @endphp
    <div class="border-b border-neutral-800 pb-5">
        <h4 class="text-sm font-semibold text-white mb-3">Cena</h4>
        <div
            x-data="rangeSlider({
                min: {{ $priceRange['min'] }},
                max: {{ $priceRange['max'] }},
                step: {{ $priceStep }},
                onChange: (min, max) => $wire.updatePrice(min, max)
            })"
            x-init="init()"
            wire:ignore
        >
            <div class="flex items-center gap-2 mb-4">
                <input type="number"
                       x-model="min"
                       @change="$wire.updatePrice(min, max)"
                       class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
                <span class="text-neutral-600 text-xs shrink-0">—</span>
                <input type="number"
                       x-model="max"
                       @change="$wire.updatePrice(min, max)"
                       class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
            </div>
            <div x-ref="slider" class="mx-1 me-3"></div>
        </div>
    </div>
@endif

@foreach($this->filterAttributes as $attribute)
    <div x-data="{ open: true }" class="border-b border-neutral-800 pb-5 last:border-0">
        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
            <h4 class="text-sm font-semibold text-white">
                {{ $attribute->name }}
                @if($attribute->unit)
                    <span class="text-neutral-500 font-normal">({{ $attribute->unit }})</span>
                @endif
            </h4>
            <i class="fa-solid fa-chevron-down text-neutral-500 text-xs transition-transform duration-200" :class="open && 'rotate-180'"></i>
        </button>

        <div x-show="open" x-collapse class="mt-3">
            @if($attribute->type === 'select')
                <div class="space-y-2">
                    @foreach($attribute->attributeValues as $value)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox"
                                   wire:click.debounce.300ms="toggleValue({{ $value->id }})"
                                   @checked(in_array($value->id, $selectedValues))
                                   class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-white focus:ring-neutral-500 focus:ring-offset-0 cursor-pointer">

                            <span class="text-sm text-neutral-400 group-hover:text-white transition-colors">
                                {{ $value->value }}
                            </span>
                        </label>
                    @endforeach
                </div>

            @elseif($attribute->type === 'number')
                @php
                    $bounds = $attributeRanges[$attribute->id] ?? ['min' => 0, 'max' => 100];
                    $step = ($bounds['max'] - $bounds['min']) > 10 ? 1 : 0.1;
                @endphp
                <div
                    x-data="rangeSlider({
                        min: {{ $bounds['min'] }},
                        max: {{ $bounds['max'] }},
                        step: {{ $step }},
                        onChange: (min, max) => $wire.updateRange({{ $attribute->id }}, min, max)
                    })"
                    x-init="init()"
                    wire:ignore
                >
                    <div class="flex items-center gap-2 mb-4">
                        <input type="number"
                               x-model="min"
                               @change="$wire.updateRange({{ $attribute->id }}, min, max)"
                               class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
                        <span class="text-neutral-600 text-xs shrink-0">-</span>
                        <input type="number"
                               x-model="max"
                               @change="$wire.updateRange({{ $attribute->id }}, min, max)"
                               class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
                    </div>

                    <div x-ref="slider" class="mx-1 me-3"></div>
                </div>

            @elseif($attribute->type === 'bool')
                <label class="flex items-center gap-2.5 cursor-pointer group/check">
                    <input type="checkbox"
                           wire:click.debounce.300ms="toggleBool({{ $attribute->id }})"
                           @checked(!empty($boolFilters[$attribute->id]))
                           class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-white focus:ring-neutral-500 focus:ring-offset-0 cursor-pointer">

                    <span class="text-sm text-neutral-400 group-hover/check:text-white transition-colors">
                        Ano
                    </span>
                </label>
            @endif
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rangeSlider', ({ min, max, step, onChange }) => ({
            min,
            max,
            step,
            dragging: false,
            slider: null,

            init() {
                this.slider = noUiSlider.create(this.$refs.slider, {
                    start: [this.min, this.max],
                    connect: true,
                    step: this.step,
                    range: { min: this.min, max: this.max }
                });

                this.slider.on('start', () => this.dragging = true);

                // values[0] min, values[1] max
                this.slider.on('end', (values) => {
                    this.dragging = false;
                    onChange(parseFloat(values[0]), parseFloat(values[1]));
                    // this.$wire.updateRange(
                    //     attributeId,
                    //     parseFloat(values[0]),
                    //     parseFloat(values[1])
                    // );
                });

                this.slider.on('update', (values) => {
                    this.min = parseFloat(values[0]);
                    this.max = parseFloat(values[1]);
                });
            }
        }));
    });
</script>
