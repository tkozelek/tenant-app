@props(['groupedAttributes'])

@if($groupedAttributes->isNotEmpty())
    <div class="border border-neutral-800 rounded-xl overflow-hidden mt-4">
        <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wider px-4 py-3 border-b border-neutral-800">Parametre</h3>
        @foreach($groupedAttributes as $attributeName => $values)
            <div class="flex items-start justify-between px-4 py-2.5 border-b border-neutral-800/50 last:border-0">
                <span class="text-sm text-neutral-400">{{ $attributeName }}</span>
                <span class="text-sm text-white font-medium text-right ml-4">
                    @foreach($values as $gpa)
                        {{ $gpa->attributeValue?->value ?? $gpa->custom_value }}@if($gpa->attribute?->unit) {{ $gpa->attribute->unit }}@endif@unless($loop->last), @endunless
                    @endforeach
                </span>
            </div>
        @endforeach
    </div>
@endif
