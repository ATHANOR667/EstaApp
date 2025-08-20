<div>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl relative min-h-[600px] flex flex-col">
        <!-- En-tête du calendrier et sélecteur de vue -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <!-- Navigation (Année et Mois/Jour) -->
            <div class="flex items-center space-x-2">
                @if ($viewMode === 'month')
                    {{-- Bouton Année Précédente --}}
                    <button wire:click="goToPreviousYear" class="p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                    </button>
                @endif
                {{-- Bouton Période Précédente (Mois ou Jour) --}}
                <button wire:click="goToPrevious" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                {{-- Titre de la période actuelle --}}
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 text-center capitalize">
                    {{ $this->currentPeriodTitle }}
                </h2>
                {{-- Bouton Période Suivante (Mois ou Jour) --}}
                <button wire:click="goToNext" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                @if ($viewMode === 'month')
                    {{-- Bouton Année Suivante --}}
                    <button wire:click="goToNextYear" class="p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7" /></svg>
                    </button>
                @endif
            </div>

            <!-- Sélecteur de vue et bouton Ajouter Prestation -->
            <div class="flex flex-wrap items-center gap-2">
                {{-- Sélecteur de mode de vue --}}
                <div class="flex space-x-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-full shadow-inner">
                    <button wire:click="setViewMode('month')"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                        {{ $viewMode === 'month' ? 'bg-blue-600 text-white shadow' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Mois
                    </button>
                    <button wire:click="setViewMode('day')"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                        {{ $viewMode === 'day' ? 'bg-blue-600 text-white shadow' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Jour
                    </button>
                </div>
                {{-- Bouton Ajouter Prestation --}}
                <button wire:click="openPrestationFormModal"
                        class="ml-4 px-4 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    + Ajouter Prestation
                </button>
            </div>
        </div>

        <div class="relative flex-grow">
            {{-- Indicateur de chargement --}}
            <div wire:loading wire:target="setViewMode openDayDetails goToPreviousYear goToPrevious goToNextYear goToNext openPrestationFormModal" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center rounded-lg z-10">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Chargement...</span>
                </div>
            </div>

            {{-- Le contenu du calendrier, caché pendant le chargement --}}
            <div wire:loading.class.add="opacity-50" wire:loading.class.remove="opacity-100" class="transition-opacity duration-300">
                @if ($viewMode === 'month')
                    @livewire('admin.calendar.view-mode.month-view', [
                    'currentDate' => $currentDate,
                    'prestations' => $prestations,
                    ], key('month-view-' . $currentDate->format('Y-m-d') . '-' . $viewMode))
                @elseif ($viewMode === 'day')
                    @livewire('admin.calendar.view-mode.day-view', [
                    'currentDate' => $currentDate,
                    'prestations' => $prestations,
                    ], key('day-view-' . $currentDate->format('Y-m-d') . '-' . $viewMode))
                @endif
            </div>
        </div>

        <!-- Modales (appel des composants enfants) -->


        {{-- Modale des détails de prestation --}}
        @livewire('admin.calendar.prestation.prestation-details-modal')

        {{-- Modale du formulaire de prestation (création/édition) --}}
        @livewire('admin.calendar.prestation.prestation-form-modal')

        {{-- Modale pour la liste des contrats d'une prestation --}}
        @livewire('admin.calendar.contrat.contrat-list-modal')

        {{-- Modale pour le formulaire de contrat (création/édition) --}}
        @livewire('admin.calendar.contrat.contrat-form-modal')

        {{-- Modale pour le formulaire d'envoi du contrat  --}}
        @livewire('admin.calendar.contrat.docu-sign-send-modal')
    </div>

    <style>
        /* Styles personnalisés pour la scrollbar (pour les événements dans les cellules) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0; /* gray-300 */
            border-radius: 2px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563; /* gray-600 */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0; /* gray-400 */
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280; /* gray-500 */
        }
    </style>


</div>
