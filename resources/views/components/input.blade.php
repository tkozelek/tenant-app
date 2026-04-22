<div {{ $attributes->merge(['class' => '']) }}>
    <x-input-label :for="$name" :value="$label" />

    <x-text-input
        :disabled="$disabled"
        :id="$name"
        class="block mt-1 w-full"
        :type="$type"
        :name="$name"
        :icon="$faIcon"
        :value="$value"
        :required="$req"
        :autocomplete="$autocomplete ?? $name"
        :placeholder="$placeholder"
    />

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
