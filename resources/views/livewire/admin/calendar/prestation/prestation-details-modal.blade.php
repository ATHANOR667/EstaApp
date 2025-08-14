<div>
    @php use Carbon\Carbon; @endphp

    @if ($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50"
             x-data="{ show: @entangle('showModal') }" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.away="show = false; $wire.closeModal()">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto relative transform transition-all duration-300 overflow-y-auto max-h-[90vh]">
                <button wire:click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Détails de la prestation
                </h3>

                {{-- Messages de session --}}
                @if (session()->has('success'))
                    <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($prestation->id)
                    <div class="mb-4 p-4 rounded-lg
                                {{ $prestation->status == 'Accepté' ? 'bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700' : '' }}
                                {{ $prestation->status == 'En attente de réponse' ? 'bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700' : '' }}
                                {{ $prestation->status == 'Rédigé' ? 'bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700' : '' }}
                                {{ $prestation->status == 'Rejeté' ? 'bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700' : '' }}
                                {{ $prestation->status == 'En cours de rédaction' ? 'bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700' : '' }}
                                {{ $prestation->status == 'N/A' ? 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' : '' }}">
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ $prestation->artiste->nom ?? 'Artiste non défini' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Organisateur: {{ $prestation->nom_structure_contractante }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Date: {{ Carbon::parse($prestation->date_prestation)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Type: {{ $prestation->type_evenement }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Heure: {{ Carbon::parse($prestation->heure_debut_prestation)->format('H:i') }} - {{ Carbon::parse($prestation->heure_fin_prevue)->format('H:i') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lieu: {{ $prestation->lieu_prestation }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Statut de la prestation:
                            <span class="font-medium
                                {{ $prestation->status == 'Accepté' ? 'text-green-700 dark:text-green-200' : '' }}
                                {{ $prestation->status == 'En attente de réponse' ? 'text-yellow-700 dark:text-yellow-200' : '' }}
                                {{ $prestation->status == 'Rédigé' ? 'text-blue-700 dark:text-blue-200' : '' }}
                                {{ $prestation->status == 'Rejeté' ? 'text-red-700 dark:text-red-200' : '' }}
                                {{ $prestation->status == 'En cours de rédaction' ? 'text-purple-700 dark:text-purple-200' : '' }}
                                {{ $prestation->status == 'N/A' ? 'text-gray-700 dark:text-gray-200' : '' }}">
                                {{ $prestation->status }}
                            </span>
                        </p>
                        @if ($prestation->observations_particulieres)
                            <p class="text-sm text-gray-700 dark:text-gray-200 mt-2">Notes: {{ $prestation->observations_particulieres }}</p>
                        @endif

                        {{-- Section des boutons avec un spinner --}}
                        <div class="mt-4 flex flex-col sm:flex-row flex-wrap justify-end gap-2">
                            <button wire:click="editPrestation" wire:loading.attr="disabled"
                                    @if ($hasAcceptedContrat) disabled @endif
                                    class="px-4 py-2 rounded-md transition-colors duration-200 flex items-center justify-center
                                    @if ($hasAcceptedContrat)
                                        bg-gray-400 text-gray-700 cursor-not-allowed dark:bg-gray-600 dark:text-gray-400
                                    @else
                                        bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                    @endif">
                                <span wire:loading.remove wire:target="editPrestation">Modifier</span>
                                <span wire:loading wire:target="editPrestation">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>

                            <button wire:click="openContratList" wire:loading.attr="disabled"
                                    class="px-4 py-2 rounded-md bg-purple-500 text-white hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 flex items-center justify-center">
                                <span wire:loading.remove wire:target="openContratList">Voir les contrats</span>
                                <span wire:loading wire:target="openContratList">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>

                            <button wire:click="deletePrestation" wire:loading.attr="disabled"
                                    onclick="confirm('Êtes-vous sûr de vouloir supprimer cette prestation ? Cela supprimera aussi tous les contrats associés.') || event.stopImmediatePropagation()"
                                    @if ($hasAcceptedContrat) disabled @endif
                                    class="px-4 py-2 rounded-md transition-colors duration-200 flex items-center justify-center
                                    @if ($hasAcceptedContrat)
                                        bg-gray-400 text-gray-700 cursor-not-allowed dark:bg-gray-600 dark:text-gray-400
                                    @else
                                        bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                                    @endif">
                                <span wire:loading.remove wire:target="deletePrestation">Supprimer</span>
                                <span wire:loading wire:target="deletePrestation">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-300">Détails de la prestation non trouvés.</p>
                @endif

                <div class="mt-6 flex justify-end">
                    <button wire:click="closeModal"
                            class="px-5 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
