@can('platform.access')
    <x-responsive-nav-link href="/admin">
        Administracia
    </x-responsive-nav-link>
@endcan

<x-responsive-nav-link :href="route('profile.edit')">
    {{ __('Profile') }}
</x-responsive-nav-link>

<!-- Authentication -->
<form method="POST" action="{{ route('logout') }}">
    @csrf

    <x-responsive-nav-link :is-button="true"
                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
        {{ __('Log Out') }}
    </x-responsive-nav-link>
</form>
