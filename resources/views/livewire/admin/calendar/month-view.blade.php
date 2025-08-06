<div class="flex-grow">
    {{-- En-têtes des jours de la semaine --}}
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
        <div>Lun</div>
        <div>Mar</div>
        <div>Mer</div>
        <div>Jeu</div>
        <div>Ven</div>
        <div>Sam</div>
        <div>Dim</div>
    </div>

    {{-- Grille des jours du mois --}}
    <div class="grid grid-cols-7 gap-1 h-full">
        <!-- Jours vides avant le début du mois (pour aligner le 1er jour) -->
        @for ($i = 0; $i < $blankDaysBefore; $i++)
            <div class="h-28 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500"></div>
        @endfor

        <!-- Jours du mois -->
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $currentDay = $firstDayOfMonth->copy()->day($day); // Crée une instance Carbon pour le jour actuel de la boucle
                $dateString = $currentDay->format('Y-m-d'); // Format de date pour la communication Livewire
                $isToday = $currentDay->isToday(); // Vérifie si c'est le jour actuel
                $dayPrestations = $this->getPrestationsForDay($dateString); // Récupère les prestations pour ce jour
            @endphp
            <div wire:click="openDayDetails('{{ $dateString }}')" {{-- Au clic, émet l'événement pour DayView --}}
            class="h-28 rounded-lg p-2 flex flex-col cursor-pointer transition-all duration-200
                 {{ $isToday ? 'bg-blue-100 dark:bg-blue-700 border-2 border-blue-500 dark:border-blue-400' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600' }}
                 relative overflow-hidden group">
                {{-- Numéro du jour --}}
                <span class="text-sm font-semibold {{ $isToday ? 'text-blue-800 dark:text-blue-100' : 'text-gray-800 dark:text-gray-100' }}">
                    {{ $day }}
                </span>

                {{-- Affichage des indicateurs de prestation si des prestations existent --}}
                @if ($dayPrestations->isNotEmpty())
                    <div class="mt-1 flex flex-wrap gap-1 custom-scrollbar overflow-y-auto max-h-[calc(100%-24px)]">
                        {{-- Affiche les 3 premières prestations pour un aperçu --}}
                        @foreach ($dayPrestations->take(3) as $prestation)
                            <div class="flex items-center text-xs px-2 py-1 rounded-full text-white
                                        {{ $prestation->status === 'redigee' ? 'bg-blue-500' : 'bg-gray-500' }}"
                                 {{-- Style dynamique pour la couleur de l'artiste --}}
                                 style="{{ $prestation->artiste && $prestation->artiste->couleur ? 'background-color:' . $prestation->artiste->couleur . ';' : '' }}">
                                @if ($prestation->artiste && $prestation->artiste->photo)
                                    {{-- Photo de l'artiste --}}
                                    <img src="{{ $prestation->artiste->photo }}" alt="{{ $prestation->artiste->nom }}" class="w-4 h-4 rounded-full mr-1 object-cover">
                                @else
                                    {{-- Initiale de l'artiste si pas de photo --}}
                                    <div class="w-4 h-4 rounded-full mr-1 flex items-center justify-center bg-white text-gray-800 font-bold text-xs">
                                        {{ substr($prestation->nom_artiste_groupe, 0, 1) }}
                                    </div>
                                @endif
                                <span class="truncate">{{ $prestation->nom_artiste_groupe }}</span>
                            </div>
                        @endforeach
                        {{-- Indique le nombre de prestations supplémentaires --}}
                        @if ($dayPrestations->count() > 3)
                            <span class="text-xs text-gray-600 dark:text-gray-300 ml-1">+{{ $dayPrestations->count() - 3 }}</span>
                        @endif
                    </div>
                @endif
            </div>
        @endfor
    </div>
</div>
