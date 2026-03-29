<div class="relative" x-data="{ open: false }">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button
                class="flex items-center justify-center w-8 h-8 rounded-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="">
                @can('manageRoles', $tenant)
                    <x-dropdown-link :href="route('tenant-user.index', $tenant->slug)"
                                     class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-users-gear w-5 mr-2 text-indigo-500 dark:text-indigo-400"></i>
                        Správa rolí
                    </x-dropdown-link>
                @endcan

                @can('update', $tenant)
                    <x-dropdown-link :href="route('tenant.edit', $tenant->slug)"
                                     class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-pen-to-square w-5 mr-2 text-blue-500 dark:text-blue-400"></i>
                        Upraviť obchod
                    </x-dropdown-link>
                @endcan

                @can('changeOwner', $tenant)
                    <x-dropdown-link x-data=""
                                     x-on:click.prevent="$dispatch('open-modal', 'confirm-tenant-ownership-change-{{ $tenant->id }}')"
                                     href="#"
                                     class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                        <i class="fa-solid fa-user w-5 mr-2 text-indigo-500 dark:text-indigo-400"></i>
                        Zmena majiteľa
                    </x-dropdown-link>
                @endcan

                @can('delete', $tenant)
                    <x-dropdown-link x-data=""
                                     x-on:click.prevent="$dispatch('open-modal', 'confirm-tenant-deletion-{{ $tenant->id }}')"
                                     class="flex items-center px-4 py-2 text-sm !text-red-600 !dark:text-red-400 !hover:bg-red-50 !dark:hover:bg-red-900/20 transition-colors cursor-pointer"
                                     href="#">
                        <i class="fa-solid fa-trash-can w-5 mr-2"></i>
                        Vymazať obchod
                    </x-dropdown-link>
                @endcan
            </div>
        </x-slot>
    </x-dropdown>
</div>
