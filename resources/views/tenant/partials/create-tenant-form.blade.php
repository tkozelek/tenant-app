<section class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ isset($tenant) ? 'Upraviť obchod' : 'Vytvoriť nový obchod' }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ isset($tenant) ? 'Upravte informácie o vašom obchode nižšie.' : 'Vyplňte základné informácie pre založenie nového obchodu.' }}
        </p>
    </header>

    <form
        method="POST"
        action="{{ isset($tenant) ? route('tenant.update', $tenant->slug) : route('tenant.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @if(isset($tenant))
            @method('PATCH')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <x-input-label for="name" value="Názov obchodu" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $tenant->name ?? '')" required autofocus placeholder="Napr. Môj super obchod" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Slug -->
            <div>
                <x-input-label for="slug" value="URL adresa (slug)" />
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                        {{ config('app.url') }}/
                    </span>
                    <x-text-input id="slug" name="slug" type="text" class="rounded-l-none" :value="old('slug', $tenant->slug ?? '')" required placeholder="moj-obchod" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('slug')" />
            </div>
        </div>

        <!-- Short description -->
        <div>
            <x-input-label for="short_description" value="Krátky popis (SEO)" />
            <x-text-input id="short_description" name="short_description" type="text" class="mt-1 block w-full" :value="old('short_description', $tenant->short_description ?? '')" placeholder="Stručný popis pre vyhľadávače a zoznamy (max 160 znakov)" />
            <x-input-error class="mt-2" :messages="$errors->get('short_description')" />
        </div>

        <!-- Full description (TinyMCE) -->
        <div>
            <x-forms.tinymce-editor name="description" label="Detailný popis obchodu" :value="old('description', $tenant->description ?? '')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Image -->
            <x-forms.file-input
                name="image"
                label="Logo / Hlavný obrázok"
                help-text="PNG, JPG, GIF do 2MB"
                :current-image="isset($tenant) && $tenant->hasMedia('images') ? $tenant->getImageUrl() : null"
                delete-action="delete-image-form"
            />

            <!-- Title -->
            <x-forms.file-input
                name="title_image"
                label="Titulný obrázok (Banner)"
                help-text="Odporúčané: 1200x400px"
                :current-image="isset($tenant) && $tenant->hasMedia('titles') ? $tenant->getTitleImageUrl() : null"
                delete-action="delete-title-image-form"
            />
        </div>

        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/30">
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Viditeľnosť obchodu</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ak je vypnuté, obchod uvidia iba administrátori.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_public" value="1" class="sr-only peer" {{ old('is_public', $tenant->is_public ?? true) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('dashboard.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                Zrušiť
            </a>
            <x-primary-button class="!py-3 !px-6 !text-base">
                {{ isset($tenant) ? 'Uložiť zmeny' : 'Vytvoriť obchod' }}
            </x-primary-button>
        </div>
    </form>

    <!-- Hidden Delete Forms -->
    @if(isset($tenant))
        <form id="delete-image-form" action="{{ route('tenant.image', ['tenant' => $tenant, 'title' => 0]) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
        <form id="delete-title-image-form" action="{{ route('tenant.image', ['tenant' => $tenant, 'title' => 1]) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endif
</section>
