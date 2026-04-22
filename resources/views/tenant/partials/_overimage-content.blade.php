<div class="absolute top-0 left-0 w-full p-3 flex justify-between items-start z-20 pointer-events-none">

    <div class="pointer-events-auto">
        @if($tenant->is_public)
            <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wide bg-green-700 text-white">
                    Verejný
                </span>
        @else
            <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wide bg-neutral-700 text-white">
                    Súkromný
                </span>
        @endif
    </div>

    @canany(['update', 'assign-roles', 'change-owner', 'manage-tenant'], $tenant)
        <div class="pointer-events-auto">
            @include('tenant.partials.tenant-options')
        </div>
    @endcanany
</div>
@can('change-owner', $tenant)
    <x-modal
        name="confirm-tenant-ownership-change-{{ $tenant->id }}"
        :show="$errors->{'changeOwner_'.$tenant->id}->isNotEmpty()"
        focusable
    >
        <form method="post" action="{{ route('tenant.change-owner', $tenant->slug) }}" class="p-6">
            @csrf
            @method('patch')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Zmena majiteľa obchodu {{ $tenant->name }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Zadajte e-mail nového majiteľa a vaše heslo pre potvrdenie.
            </p>
-
            <div class="mt-6">
                <x-input
                    name="email"
                    type="email"
                    label="E-mail"
                    placeholder="E-mail nového majiteľa"
                    required
                />
            </div>

            <div class="mt-6">
                <x-input
                    name="password"
                    type="password"
                    label="Heslo"
                    placeholder="••••••••"
                    required
                />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    Zmeniť majiteľa
                </x-primary-button>
            </div>
        </form>
    </x-modal>
@endcan

@can('delete', $tenant)
    <x-modal
        name="confirm-tenant-deletion-{{ $tenant->id }}"
        :show="$errors->{'tenantDeletion_'.$tenant->id}->isNotEmpty()"
        focusable
    >
        <form method="post" action="{{ route('tenant.destroy', $tenant->slug) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("Are you sure you want to delete shop :tenant ?", ['tenant' => $tenant->name]) }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Po vymazaní obchodu budú všetky jeho dáta natrvalo odstránené. Zadajte heslo pre potvrdenie.
            </p>

            <div class="mt-6">
                <x-input-label for="password-{{ $tenant->id }}" value="{{ __('Password') }}" class="sr-only"/>
                <x-text-input
                    id="password-{{ $tenant->id }}"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                    required
                />
                <x-input-error :messages="$errors->{'tenantDeletion_'.$tenant->id}->get('password')" class="mt-2"/>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Shop') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
@endcan
