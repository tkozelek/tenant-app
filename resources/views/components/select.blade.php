<div>
    @if($label)
        <x-input-label :for="$name" :value="$label" />
    @endif

    <select
        {{ $attributes->merge(['class' => 'bg-neutral-900 border border-neutral-700 rounded-lg text-sm text-white px-3 py-2.5 focus:outline-none focus:border-neutral-500 transition-colors' . ($label ? ' block mt-1 w-full' : '')]) }}
        @disabled($disabled)
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $req ? 'required' : '' }}
    >
        @if($placeholder)
            <option value="" {{ is_null($value) || $value === '' ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $labelOption)
            <option value="{{ $key }}" {{ $value == $key ? 'selected' : '' }}>
                {{ $labelOption }}
            </option>
        @endforeach
    </select>

    @if($label)
        <x-input-error :messages="$errors->get($name)" class="mt-2" />
    @endif
</div>
