<div class="mt-6">
    <div class="container mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">

            <div class="flex flex-col sm:flex-row items-center justify-between p-4 gap-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 rounded-t-xl">
                <div class="relative w-full sm:w-auto">
                    <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="w-full sm:w-auto inline-flex items-center justify-between sm:justify-start px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700 transition-colors" type="button">
                        <span class="flex items-center">
                            <i class="fa-solid fa-filter mr-2 text-gray-400"></i>
                            {{ $selectedRole ? $this->roles->find($selectedRole)->name : 'Všetky role' }}
                        </span>
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>

                    <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600">
                        <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
                            <li>
                                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer" wire:click="$set('selectedRole', '')">
                                    <input type="radio" id="radio-all" name="filter-radio" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600" {{ $selectedRole === '' ? 'checked' : '' }}>
                                    <label for="radio-all" class="w-full ms-2 text-sm font-medium rounded cursor-pointer">Všetky</label>
                                </div>
                            </li>
                            @isset($this->roles)
                                @foreach($this->roles as $role)
                                    <li>
                                        <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer" wire:click="$set('selectedRole', '{{ $role->id }}')">
                                            <input type="radio" id="radio-{{ $role->id }}" name="filter-radio" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600" {{ $selectedRole == $role->id ? 'checked' : '' }}>
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
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500" placeholder="Hľadať používateľa...">
                </div>
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" wire:click="sortBy('first_name')">
                                <div class="flex items-center">
                                    Meno
                                    @if($sortField === 'first_name')
                                        <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" wire:click="sortBy('email')">
                                <div class="flex items-center">
                                    E-mail
                                    @if($sortField === 'email')
                                        <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fa-solid fa-sort ml-1 text-gray-300"></i>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">Rola</th>
                            <th scope="col" class="px-6 py-3">Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @isset($this->users)
                        @forelse($this->users as $user)
                            @php
                                $userRole = $user->roles->where('pivot.tenant_id', $tenant->id)->first();
                                $isOwner = $user->id === $tenant->owner_id;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold mr-3">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            @if($isOwner)
                                                <span class="text-[10px] bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-800">Majiteľ</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($isOwner)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <i class="fa-solid fa-crown mr-1.5 text-amber-500"></i>
                                            Majiteľ
                                        </span>
                                    @else
                                        <select
                                            wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
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
                                            class="font-medium text-red-600 dark:text-red-500 hover:underline hover:text-red-800 dark:hover:text-red-400 transition-colors"
                                        >
                                            <i class="fa-solid fa-trash-can mr-1"></i> Odstrániť
                                        </button>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600 text-xs italic">Nedá sa odstrániť</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-users-slash text-3xl mb-3 text-gray-300 dark:text-gray-600"></i>
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
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
                    {{ $this->users->links() }}
                </div>
            @endisset
        </div>
    </div>
</div>
