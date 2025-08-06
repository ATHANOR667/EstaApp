<div>
    {{-- Stop trying to control. --}}

    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            {{-- Overlay --}}
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"></div>

            {{-- Modal Panel --}}
            <div x-show="show" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Créer un Nouvel Administrateur</h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createAdmin" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Nom --}}
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                            <input type="text" id="nom" wire:model.defer="nom"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom') border-red-500 @enderror">
                            @error('nom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                            <input type="text" id="prenom" wire:model.defer="prenom"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('prenom') border-red-500 @enderror">
                            @error('prenom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (optionnel)</label>
                            <input type="email" id="email" wire:model.defer="email"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                            <input type="text" id="telephone" wire:model.defer="telephone"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telephone') border-red-500 @enderror">
                            @error('telephone') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays (optionnel)</label>
                            <input type="text" id="pays" wire:model.defer="pays"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pays') border-red-500 @enderror">
                            @error('pays') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville (optionnel)</label>
                            <input type="text" id="ville" wire:model.defer="ville"
                                   class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ville') border-red-500 @enderror">
                            @error('ville') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Photo de Profil --}}
                        <div>
                            <label for="photoProfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil (optionnel)</label>
                            <input type="file" id="photoProfil" wire:model="photoProfil"
                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0 file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                      dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                            @error('photoProfil') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @if ($photoProfil)
                                <img src="{{ $photoProfil->temporaryUrl() }}" class="mt-2 h-24 w-24 object-cover rounded-full shadow-md" alt="Prévisualisation Photo Profil">
                            @endif
                        </div>

                        {{-- Pièce d'Identité Recto --}}
                        <div>
                            <label for="pieceIdentiteRecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité Recto (optionnel)</label>
                            <input type="file" id="pieceIdentiteRecto" wire:model="pieceIdentiteRecto"
                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0 file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                      dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                            @error('pieceIdentiteRecto') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @if ($pieceIdentiteRecto)
                                <img src="{{ $pieceIdentiteRecto->temporaryUrl() }}" class="mt-2 h-24 w-auto object-cover rounded-md shadow-md" alt="Prévisualisation Recto">
                            @endif
                        </div>

                        {{-- Pièce d'Identité Verso --}}
                        <div>
                            <label for="pieceIdentiteVerso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité Verso (optionnel)</label>
                            <input type="file" id="pieceIdentiteVerso" wire:model="pieceIdentiteVerso"
                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0 file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                      dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                            @error('pieceIdentiteVerso') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @if ($pieceIdentiteVerso)
                                <img src="{{ $pieceIdentiteVerso->temporaryUrl() }}" class="mt-2 h-24 w-auto object-cover rounded-md shadow-md" alt="Prévisualisation Verso">
                            @endif
                        </div>

                        {{-- Attribution des rôles --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attribuer les Rôles</label>
                            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @forelse ($this->availableRoles as $role)
                                    <label for="role-{{ $role->id }}" class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" id="role-{{ $role->id }}" value="{{ $role->id }}" wire:model.defer="selectedRoles"
                                               class="form-checkbox h-5 w-5 text-blue-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded
                                                  focus:ring-blue-500 dark:focus:ring-blue-500">
                                        <span class="ml-2 text-base">{{ $role->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 col-span-full">Aucun rôle disponible.</p>
                                @endforelse
                            </div>
                            @error('selectedRoles') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @error('selectedRoles.*') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" wire:click="closeModal"
                                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Créer l'Administrateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
