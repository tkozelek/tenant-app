@can('platform.access')
    <x-dropdown-link href="/admin">
        Administracia
    </x-dropdown-link>
@endcan

<x-dropdown-link :href="route('profile.edit')">
    {{ __('Nastavenia') }}
</x-dropdown-link>

<!-- Authentication -->
<form method="POST" action="{{ route('logout') }}">
    @csrf

    <x-dropdown-link :is-button="true">
        {{ __('Log Out') }}
    </x-dropdown-link>
</form>
