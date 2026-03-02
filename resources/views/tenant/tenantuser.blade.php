<x-app-layout>
    @if(isset($tenant))
        <x-header>
            Správa roli - {{ $tenant->name }}
        </x-header>
    @endif
    <div class="">
        @include('tenant.partials.add-user-tenant')

        <livewire:tenant-user-table :tenant="$tenant"/>

    </div>
</x-app-layout>


