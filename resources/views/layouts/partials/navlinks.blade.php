<x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.index')">
    {{ __('Dashboard') }}
</x-nav-link>
