<div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
             class="bg-green-100 dark:bg-green-800/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-md relative mb-4"
             role="alert">
            <strong class="font-bold">Succès !</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="show = false">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 3.151a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.152 7.348a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-3.151a1.2 1.2 0 1 1 1.697 1.697L11.697 10l3.151 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
             class="bg-red-100 dark:bg-red-800/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-md relative mb-4"
             role="alert">
            <strong class="font-bold">Erreur !</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="show = false">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 3.151a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.152 7.348a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-3.151a1.2 1.2 0 1 1 1.697 1.697L11.697 10l3.151 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    {{-- Section Création de Rôle --}}
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Créer un Nouveau Rôle</h3>
    <div class="mb-6 flex flex-col sm:flex-row items-stretch sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 relative">
        <div class="flex-grow">
            <input type="text" wire:model.live.debounce.300ms="newRoleName" wire:keydown.enter="createRole"
                   placeholder="Nom du nouveau rôle"
                   class="block w-full px-4 py-2 text-base rounded-md shadow-sm
                          border-gray-300 dark:border-gray-600
                          dark:bg-gray-700 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition duration-150 ease-in-out
                          @error('newRoleName') border-red-500 ring-red-500 @enderror">
            @error('newRoleName') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>
        <button wire:click="createRole"
                class="min-w-max px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-md
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-gray-800
                       transition duration-150 ease-in-out
                       flex items-center justify-center space-x-2">
            <span wire:loading.remove wire:target="createRole">Créer le Rôle</span>
            <span wire:loading wire:target="createRole" class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Création...</span>
            </span>
        </button>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    {{-- Section Liste des Rôles --}}
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Gérer les Rôles Existants</h3>
    <div class="flex flex-col md:flex-row gap-6">
        <div class="md:w-1/3">
            <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-3">Sélectionner un Rôle :</h4>
            <ul class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner divide-y divide-gray-200 dark:divide-gray-600">
                @forelse ($roles as $role)
                    <li wire:key="role-{{ $role->id }}"
                        class="p-4 flex items-center justify-between cursor-pointer
                               {{ $selectedRoleId == $role->id ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-bold' : 'hover:bg-gray-100 dark:hover:bg-gray-600' }}
                               rounded-lg transition-colors duration-150 ease-in-out"
                        wire:click="selectRole({{ $role->id }})">
                        <span class="{{ $selectedRoleId == $role->id ? 'text-blue-800 dark:text-blue-200' : 'text-gray-700 dark:text-gray-100' }}">
                            {{ $role->name }}
                        </span>
                        <button wire:click.stop="deleteRole({{ $role->id }})"
                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-600 p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-150"
                                title="Supprimer ce rôle"
                                @if (in_array($role->name, ['super-admin', 'admin'])) disabled @endif>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </li>
                @empty
                    <li class="p-4 text-gray-500 dark:text-gray-400">Aucun rôle trouvé.</li>
                @endforelse
            </ul>
        </div>

        {{-- Section Gestion des Permissions pour le Rôle Sélectionné --}}
        <div class="md:w-2/3 bg-gray-500/5 dark:bg-gray-700/30 p-4 rounded-lg shadow-inner">
            @if ($selectedRole)
                <h4 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-3">Permissions pour "<span class="font-bold text-blue-600 dark:text-blue-400">{{ $selectedRole->name }}</span>"</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($this->allPermissionsGrouped as $categorie => $permissionsOfCategory)
                        <div class="border-2 rounded-md p-3 pb-4 relative
                                    @if($categorie == 'super-admin') border-red-500 dark:border-red-600 bg-red-50 dark:bg-red-950/40 @elseif($categorie == 'admin') border-purple-500 dark:border-purple-600 bg-purple-50 dark:bg-purple-950/40 @elseif($categorie == 'user') border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-950/40 @else border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 @endif
                                    ">
                            <h5 class="font-semibold text-gray-800 dark:text-gray-100 mb-2 pb-2 border-b border-gray-200 dark:border-gray-600
                                {{ $categorie == 'super-admin' ? 'text-red-700 dark:text-red-400' : '' }}
                                {{ $categorie == 'admin' ? 'text-purple-700 dark:text-purple-400' : '' }}
                                {{ $categorie == 'user' ? 'text-green-700 dark:text-green-400' : '' }}
                                ">
                                {{ ucfirst(str_replace('_', ' ', $categorie ?: 'Autres')) }} Permissions
                                <span class="absolute top-0 right-0 -mt-2 -mr-2 px-2 py-1 text-xs font-bold rounded-full
                                    {{ $categorie == 'super-admin' ? 'bg-red-500 text-white' : '' }}
                                    {{ $categorie == 'admin' ? 'bg-purple-500 text-white' : '' }}
                                    {{ $categorie == 'user' ? 'bg-green-500 text-white' : '' }}
                                    @if (!in_array($categorie, ['super-admin', 'admin', 'user'])) bg-gray-500 text-white @endif
                                    ">
                                    {{ $permissionsOfCategory->count() }}
                                </span>
                            </h5>
                            <div class="mt-2 space-y-1">
                                @foreach ($permissionsOfCategory as $permission)
                                    <label wire:key="perm-{{ $permission->id }}"
                                           class="flex items-center space-x-2 text-gray-700 dark:text-gray-200
                                                  p-2 rounded-md cursor-pointer
                                                  {{ in_array($permission->id, $rolePermissions) ? 'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 font-medium shadow-sm' : 'hover:bg-gray-100 dark:hover:bg-gray-600' }}
                                                  transition-all duration-150 ease-in-out">
                                        <input type="checkbox" wire:model.live="rolePermissions" value="{{ $permission->id }}"
                                               class="form-checkbox h-5 w-5 text-blue-600 rounded-sm border-gray-300 dark:border-gray-600
                                                      dark:bg-gray-900 dark:checked:bg-blue-600 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-gray-800">
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 col-span-full">Aucune permission disponible. Assurez-vous d'avoir des permissions dans votre base de données.</p>
                    @endforelse
                </div>
                <button wire:click="updateRolePermissions"
                        class="mt-8 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md shadow-md
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 focus:ring-offset-white dark:focus:ring-offset-gray-800
                               transition duration-150 ease-in-out
                               flex items-center justify-center space-x-2">
                    <span wire:loading.remove wire:target="updateRolePermissions">Mettre à jour les Permissions</span>
                    <span wire:loading wire:target="updateRolePermissions" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mise à jour...</span>
                    </span>
                </button>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Sélectionnez un rôle à gauche pour gérer ses permissions.</p>
            @endif
        </div>
    </div>
</div>
