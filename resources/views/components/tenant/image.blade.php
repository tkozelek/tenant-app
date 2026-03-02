@props(['image_path' => null, 'url' => null, 'name'])

<div class="mt-4 shadow rounded-lg overflow-hidden">
    <img src="{{ $url ?? asset('storage/' . $image_path) }}"
         alt="Obrázok obchodu: {{ $name }}"
        {{ $attributes->merge(['class' => 'w-full object-cover']) }}>
</div>

