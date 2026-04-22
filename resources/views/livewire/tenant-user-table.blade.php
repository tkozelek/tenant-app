<div class="mt-6">
    <div class="container mx-auto">
        <div class="bg-white dark:bg-neutral-800 rounded-md border border-neutral-200 dark:border-neutral-700">

            <div class="flex flex-col sm:flex-row items-center justify-between p-4 gap-4 bg-neutral-50 dark:bg-neutral-800/50 border-b border-neutral-200 dark:border-neutral-700 rounded-t-md">
                <div class="relative w-full sm:w-auto">
                    <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="w-full sm:w-auto inline-flex items-center justify-between sm:justify-start px-4 py-2 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded hover:bg-neutral-50 focus:outline-none focus:ring-1 focus:ring-neutral-400 dark:bg-neutral-700 dark:text-white dark:border-neutral-600 dark:hover:bg-neutral-600 transition-colors" type="button">
                        <span class="flex items-center">
                            <i class="fa-solid fa-filter mr-2 text-neutral-400"></i>
                            {{ $selectedRole ? $this->roles->find($selectedRole)->name : 'Všetky role' }}
                        </span>
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>

                    <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-neutral-100 rounded border border-neutral-200 shadow dark:bg-neutral-700 dark:border-neutral-600 dark:divide-neutral-600">
                        <ul class="p-3 space-y-1 text-sm text-neutral-700 dark:text-neutral-200" aria-labelledby="dropdownRadioButton">
                            <li>
                                <div class="flex items-center p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-600 cursor-pointer" wire:click="$set('selectedRole', '')">
                                    <input type="radio" id="radio-all" name="filter-radio" class="w-4 h-4 text-neutral-600 bg-neutral-100 border-neutral-300 focus:ring-neutral-500 dark:focus:ring-neutral-600 dark:ring-offset-neutral-800 dark:bg-neutral-700 dark:border-neutral-600" {{ $selectedRole === '' ? 'checked' : '' }}>
                                    <label for="radio-all" class="w-full ms-2 text-sm font-medium rounded cursor-pointer">Všetky</label>
                                </div>
                            </li>
                            @isset($this->roles)
                                @foreach($this->roles as $role)
                                    <li>
                                        <div class="flex items-center p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-600 cursor-pointer" wire:click="$set('selectedRole', '{{ $role->id }}')">
                                            <input type="radio" id="radio-{{ $role->id }}" name="filter-radio" class="w-4 h-4 text-neutral-600 bg-neutral-100 border-neutral-300 focus:ring-neutral-500 dark:focus:ring-neutral-600 dark:ring-offset-neutral-800 dark:bg-neutral-700 dark:border-neutral-600" {{ $selectedRole == $role->id ? 'checked' : '' }}>
                                            <label for="radio-{{ $role->id }}" class="w-full ms-2 text-sm font-medium rounded cursor-pointer">{{ $role->name }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            @endisset
                        </ul>
                    </div>
                </div>

                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-neutral-500 dark:text-neutral-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block w-full p-2 ps-10 text-sm text-neutral-900 border border-neutral-300 rounded bg-neutral-50 focus:outline-none focus:border-neutral-500 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white" placeholder="Hľadať používateľa...">
                </div>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-neutral-500 dark:text-neutral-400">
                    <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-700 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-colors" wire:click="sortBy('first_name')">
                                <div class="flex items-center">
                                    Meno
                                    @if($sortField === 'first_name')
                                        <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fa-solid fa-sort ml-1 text-neutral-300"></i>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-colors" wire:click="sortBy('email')">
                                <div class="flex items-center">
                                    E-mail
                                    @if($sortField === 'email')
                                        <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fa-solid fa-sort ml-1 text-neutral-300"></i>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">Rola</th>
                            <th scope="col" class="px-6 py-3">Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @isset($this->users)
                        @forelse($this->users as $user)
                            @php
                                $userRole = $user->roles->where('pivot.tenant_id', $tenant->id)->first();
                                $isOwner = $user->id === $tenant->owner_id;
                            @endphp
                            <tr class="bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white whitespace-nowrap">
                                    <div class="text-sm font-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($isOwner)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-600">
                                            Majiteľ
                                        </span>
                                    @else
                                        <select
                                            wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                            class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded focus:outline-none focus:border-neutral-500 block w-full p-2 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                        >
                                            <option value="" disabled {{ !$userRole ? 'selected' : '' }}>Vyberte rolu</option>
                                            @foreach($this->roles as $role)
                                                <option value="{{ $role->name }}" {{ ($userRole && $userRole->id === $role->id) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(!$isOwner)
                                        <button
                                            type="button"
                                            wire:click="removeUser({{ $user->id }})"
                                            wire:confirm="Naozaj chcete odstrániť tohto používateľa z obchodu?"
                                            class="font-medium text-neutral-700 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white hover:underline transition-colors"
                                        >
                                            <i class="fa-solid fa-trash-can mr-1"></i> Odstrániť
                                        </button>
                                    @else
                                        <span class="text-neutral-400 dark:text-neutral-600 text-xs italic">Nedá sa odstrániť</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-users-slash text-3xl mb-3 text-neutral-300 dark:text-neutral-600"></i>
                                        <p>Nenašli sa žiadni používatelia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @endisset
                    </tbody>
                </table>
            </div>

            @isset($this->users)
                <div class="p-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 rounded-b-md">
                    {{ $this->users->links() }}
                </div>
            @endisset
        </div>
    </div>
</div>
