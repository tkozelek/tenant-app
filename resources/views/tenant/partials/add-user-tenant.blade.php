<div class="container mx-auto flex justify-between items-center my-3">
    <div></div>
    <a href=""
       class="block text-white focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-blue-600 hover:bg-blue-700 focus:ring-blue-800"
       x-data=""
       x-on:click.prevent="$dispatch('open-modal', 'add-tenant-user-{{ $tenant->id }}')"><i
            class="fa-solid fa-user-plus"></i> Pridať používateľa</a>
</div>
<x-modal
    name="add-tenant-user-{{ $tenant->id }}"
    :show="$errors->{'tenantUserAddition'.$tenant->id}->isNotEmpty()"
    focusable
>
    <form method="post" action="{{ route('tenant-user.store', $tenant) }}" class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
            {{ $tenant->name }} - Pridaj použivateľa
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Vyplň nižšie uvedené pole, používateľ musí mať vytvorený účet
        </p>

        <div class="mt-6">
            <x-input-label for="email" value="{{ __('Email') }}" class="sr-only"/>
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-3/4"
                placeholder="E-mailová adresa"
            />
            <x-input-error :messages="$errors->{'tenantUserAddition'.$tenant->id}->get('email')" class="mt-2"/>

            <x-input-label for="role" value="Rola" class="sr-only"/>
            <select name="role" id="role" class="bg-white border border-gray-300 text-black dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white rounded-lg px-5 py-1 mt-4">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->{'tenantUserAddition'.$tenant->id}->get('role')" class="mt-2"/>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                Pridaj použivateľa
            </x-primary-button>
        </div>
    </form>
</x-modal>
