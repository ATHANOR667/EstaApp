<div>
    {{-- Do your work, then step back. --}}

    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-center min-h-screen">
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
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all w-full max-w-5xl max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                        {{ $admin ? 'Profil de ' . $admin->prenom . ' ' . $admin->nom : 'Chargement...' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if ($admin)
                    <form wire:submit.prevent="updateAdmin" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            {{-- Colonne de gauche: Infos Générales --}}
                            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
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

                                {{-- Mot de passe --}}
                                <div class="sm:col-span-2">
                                    <label for="password_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mot de Passe</label>
                                    <div class="relative">
                                        <input type="text" id="password_display"
                                               value="{{ $admin->password ? '********' : 'Non défini' }}"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                {{-- Passcode --}}
                                <div class="sm:col-span-2">
                                    <label for="passcode_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passcode</label>
                                    <div class="relative">
                                        <input type="text" id="passcode_display"
                                               value="{{ $admin->passcode ? '********' : 'Non défini' }}"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                {{-- Dernier changement de mot de passe --}}
                                <div class="sm:col-span-2">
                                    <label for="password_changed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dernier changement mot de passe</label>
                                    <div class="relative">
                                        <input type="text" id="password_changed_at"
                                               value="{{ $admin->password_changed_at ? $admin->password_changed_at->format('d/m/Y H:i') : 'Jamais' }}"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                {{-- Date de réinitialisation Passcode --}}
                                <div class="sm:col-span-2">
                                    <label for="passcode_reset_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut réinitialisation Passcode</label>
                                    <div class="relative">
                                        <input type="text" id="passcode_reset_date"
                                               value="{{ $admin->passcode_reset_date ? $admin->passcode_reset_date->format('d/m/Y H:i') : 'Jamais' }}"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- Colonne de droite: Photo de Profil, Pièces d'identité, QR Code --}}
                            <div class="md:col-span-1 space-y-6">
                                {{-- Photo de Profil --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil</label>
                                    <div class="flex items-center space-x-3">
                                        @if ($newPhotoProfil)
                                            <img src="{{ $newPhotoProfil->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-full shadow-md" alt="Nouvelle Photo">
                                        @elseif ($admin->photoProfil)
                                            <img src="{{ asset('storage/' . $admin->photoProfil) }}" class="h-24 w-24 object-cover rounded-full shadow-md" alt="Photo Actuelle">
                                        @else
                                            <div class="h-24 w-24 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-3xl font-bold">
                                                {{ strtoupper(substr($prenom, 0, 1)) }}{{ strtoupper(substr($nom, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <input type="file" id="newPhotoProfil" wire:model="newPhotoProfil"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            @error('newPhotoProfil') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                                            @if ($admin->photoProfil)
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePhotoProfil" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer la photo actuelle</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Pièces d'Identité (Recto/Verso) avec animation --}}
                                <div x-data="{ showPieceIdentiteRecto: @entangle('showPieceIdentiteRecto'), isFlipping: false }" class="relative perspective-1000">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité</label>
                                    <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                        <div class="flip-card-inner w-full h-full relative" :class="isFlipping ? 'flipping' : ''">
                                            {{-- Recto --}}
                                            <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden"
                                                 x-show="showPieceIdentiteRecto"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:leave="ease-in duration-300"
                                                 x-transition:leave-end="opacity-0">
                                                @if ($admin->pieceIdentiteRecto)
                                                    <img src="{{ asset('storage/' . $admin->pieceIdentiteRecto) }}" alt="Pièce d'Identité Recto"
                                                         class="object-contain max-h-full max-w-full cursor-pointer"
                                                         @click="isFlipping = true; setTimeout(() => { showPieceIdentiteRecto = !showPieceIdentiteRecto; isFlipping = false; }, 300);">
                                                    <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto (cliquez pour Verso)</span>
                                                @else
                                                    <p class="text-gray-500 dark:text-gray-400">Aucun Recto</p>
                                                @endif
                                            </div>

                                            {{-- Verso --}}
                                            <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden"
                                                 x-show="!showPieceIdentiteRecto"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:leave="ease-in duration-300"
                                                 x-transition:leave-end="opacity-0">
                                                @if ($admin->pieceIdentiteVerso)
                                                    <img src="{{ asset('storage/' . $admin->pieceIdentiteVerso) }}" alt="Pièce d'Identité Verso"
                                                         class="object-contain max-h-full max-w-full cursor-pointer"
                                                         @click="isFlipping = true; setTimeout(() => { showPieceIdentiteRecto = !showPieceIdentiteRecto; isFlipping = false; }, 300);">
                                                    <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso (cliquez pour Recto)</span>
                                                @else
                                                    <p class="text-gray-500 dark:text-gray-400">Aucun Verso</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <div>
                                            <label for="newPieceIdentiteRecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nouveau Recto (optionnel)</label>
                                            <input type="file" id="newPieceIdentiteRecto" wire:model="newPieceIdentiteRecto"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            @error('newPieceIdentiteRecto') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            @if ($admin->pieceIdentiteRecto && !$newPieceIdentiteRecto)
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePieceIdentiteRecto" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer le Recto actuel</span>
                                                </label>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="newPieceIdentiteVerso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nouveau Verso (optionnel)</label>
                                            <input type="file" id="newPieceIdentiteVerso" wire:model="newPieceIdentiteVerso"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            @error('newPieceIdentiteVerso') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            @if ($admin->pieceIdentiteVerso && !$newPieceIdentiteVerso)
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePieceIdentiteVerso" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer le Verso actuel</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- QR Code du Matricule --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code QR du Matricule</label>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-inner flex items-center justify-center">
                                        {!! QrCode::size(150)->generate($admin->matricule) !!}
                                    </div>
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Matricule: {{ $admin->matricule }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Attribution des rôles --}}
                        <div class="mb-6 border-t pt-6 border-gray-200 dark:border-gray-700">
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
                                    <p class="text-gray-500 dark:text-gray-400 col-span-full">Aucun rôle disponible pour le guard 'admin'.</p>
                                @endforelse
                            </div>
                            @error('selectedRoles') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            @error('selectedRoles.*') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" wire:click="closeModal"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                Annuler
                            </button>
                            {{-- Bouton pour générer le PDF --}}
                            <button type="button" wire:click="generatePdf"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                Générer PDF
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                Mettre à jour le Profil
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        Chargement du profil ou administrateur non trouvé...
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
