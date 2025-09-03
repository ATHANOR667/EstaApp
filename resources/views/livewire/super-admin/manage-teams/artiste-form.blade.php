<div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto p-4">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Overlay -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"></div>

        <!-- Modal Panel -->
        <div x-show="show" x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md sm:max-w-lg max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                    {{ $artisteId ? 'Éditer un Artiste' : 'Créer un Artiste' }}
                </h3>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Fermer la modale">
                    <svg class="h-6 w-6 sm:h-7 sm:w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="saveArtiste" class="p-4 sm:p-6">
                <div class="space-y-4">
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de l'Artiste</label>
                        <input type="text" id="nom" wire:model.defer="nom"
                               class="w-full px-3 py-2 text-base rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom') border-red-500 @enderror">
                        @error('nom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de l'Artiste</label>
                        @if ($newPhoto)
                            <div class="mb-2">
                                <img src="{{ $newPhoto->temporaryUrl() }}" class="h-16 w-16 object-cover rounded-full shadow-md" alt="Nouvelle photo">
                            </div>
                        @elseif ($photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $photo) }}" class="h-16 w-16 object-cover rounded-full shadow-md" alt="Photo actuelle">
                            </div>
                        @else
                            <div class="mb-2 h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold">
                                {{ strtoupper(substr($nom, 0, 1)) }}
                            </div>
                        @endif
                        <input type="file" id="photo" wire:model="newPhoto"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                        @error('newPhoto') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Couleur -->
                    <div>
                        <label for="couleur" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Couleur de l'Artiste</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="couleur" wire:model.defer="couleur"
                                   class="w-12 h-8 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('couleur') border-red-500 @enderror">
                            <div class="h-8 w-8 rounded-md" style="background-color: {{ $couleur ?? '#9ca3af' }}"></div>
                        </div>
                        @error('couleur') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 mt-6">
                    <button type="button" wire:click="closeModal"
                            class="w-full sm:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500">
                        Annuler
                    </button>
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        {{ $artisteId ? 'Sauvegarder' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
