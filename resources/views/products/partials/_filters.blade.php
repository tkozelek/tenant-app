@foreach($attributes as $attribute)
    <div class="border-b border-neutral-800 pb-5 last:border-0">
        <h4 class="text-sm font-semibold text-white mb-3">
            {{ $attribute->name }}
                <span class="text-neutral-500 font-normal">({{ $attribute->unit }})</span>
            @endif
        </h4>

        @if($attribute->type === 'select')
            <div class="space-y-2">
                @foreach($attribute->attributeValues as $value)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                               class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-white focus:ring-neutral-500 focus:ring-offset-0 cursor-pointer">
                        <span class="text-sm text-neutral-400 group-hover transition-colors">
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
                x-init="
                    let slider = noUiSlider.create($refs.slider, {
                        start: [{{ $bounds['min'] }}, {{ $bounds['max'] }}],
                        connect: true,
                        step: {{ $step }},
                        range: { min: {{ $bounds['min'] }}, max: {{ $bounds['max'] }} }
                    });
                    slider.on('start', () => dragging = true);
                    slider.on('end', () => dragging = false);
                    slider.on('update', (values) => {
                        min = parseFloat(values[0]);
                        max = parseFloat(values[1]);
                    });
                    $watch('min', v => { if (!dragging) slider.set([v, null]); });
                    $watch('max', v => { if (!dragging) slider.set([null, v]); });
                "
            >
                <div class="flex items-center gap-2 mb-4">
                    <input type="number"
                           class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
                    <span class="text-neutral-600 text-xs shrink-0">—</span>
                    <input type="number"
                           class="w-full bg-neutral-800 border border-neutral-700 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-neutral-500">
                </div>

                <div x-ref="slider" class="mx-1 me-3"></div>
            </div>

        @elseif($attribute->type === 'bool')
            <label class="flex items-center gap-2.5 cursor-pointer group/check">
                <input type="checkbox"
                       class="w-4 h-4 rounded border-neutral-600 bg-neutral-800 text-white focus:ring-neutral-500 focus:ring-offset-0 cursor-pointer">
                <span class="text-sm text-neutral-400 group-hover/check:text-white transition-colors">
                    Ano
                </span>
            </label>
        @endif
    </div>
@endforeach
