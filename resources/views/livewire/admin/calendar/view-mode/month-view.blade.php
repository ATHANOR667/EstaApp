<div class="flex-grow">
    {{-- On définit la fonction isColorDark ici pour qu'elle soit disponible dans toute la vue --}}
    @php
        function isColorDark($hexColor) {
            $hexColor = ltrim($hexColor, '#');
            if (strlen($hexColor) == 3) {
                $r = hexdec(str_repeat(substr($hexColor, 0, 1), 2));
                $g = hexdec(str_repeat(substr($hexColor, 1, 1), 2));
                $b = hexdec(str_repeat(substr($hexColor, 2, 1), 2));
            } else {
                $r = hexdec(substr($hexColor, 0, 2));
                $g = hexdec(substr($hexColor, 2, 2));
                $b = hexdec(substr($hexColor, 4, 2));
            }
            $luma = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luma < 0.5;
        }
    @endphp

    {{-- En-têtes des jours de la semaine --}}
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
        <div class="hidden sm:block">Lun</div>
        <div class="block sm:hidden">L</div>
        <div class="hidden sm:block">Mar</div>
        <div class="block sm:hidden">M</div>
        <div class="hidden sm:block">Mer</div>
        <div class="block sm:hidden">M</div>
        <div class="hidden sm:block">Jeu</div>
        <div class="block sm:hidden">J</div>
        <div class="hidden sm:block">Ven</div>
        <div class="block sm:hidden">V</div>
        <div class="hidden sm:block">Sam</div>
        <div class="block sm:hidden">S</div>
        <div class="hidden sm:block">Dim</div>
        <div class="block sm:hidden">D</div>
    </div>

    {{-- Grille des jours du mois --}}
    <div class="grid grid-cols-7 gap-1 h-full">
        @for ($i = 0; $i < $blankDaysBefore; $i++)
            <div class="h-28 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500"></div>
        @endfor

        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $currentDay = $firstDayOfMonth->copy()->day($day);
                $dateString = $currentDay->format('Y-m-d');
                $isToday = $currentDay->isToday();
                $dayPrestations = $this->getPrestationsForDay($dateString);
            @endphp
            <div wire:click="openDayDetails('{{ $dateString }}')"
                 class="h-28 rounded-lg p-2 flex flex-col cursor-pointer transition-all duration-200
                 {{ $isToday ? 'bg-blue-100 dark:bg-blue-700 border-2 border-blue-500 dark:border-blue-400' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600' }}
                 relative group">
                <span class="text-sm font-semibold {{ $isToday ? 'text-blue-800 dark:text-blue-100' : 'text-gray-800 dark:text-gray-100' }}">
                    {{ $day }}
                </span>

                @if ($dayPrestations->isNotEmpty())
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach ($dayPrestations as $prestation)
                            @php
                                $artiste = $prestation->artiste;
                                $bgColor = $artiste->couleur ?? '#cbd5e0';
                                // Appel direct de la fonction définie ci-dessus
                                $textColor = isColorDark($bgColor) ? 'text-white' : 'text-black';
                            @endphp
                            <div class="relative w-6 h-6 rounded-full overflow-hidden flex items-center justify-center border-2 border-white dark:border-gray-800"
                                 style="background-color: {{ $bgColor }};"
                                 title="{{ $prestation->artiste->nom }}">
                                @if ($artiste && $artiste->photo)
                                    <img src="{{ $artiste->photo }}" alt="{{ $artiste->nom }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-bold {{ $textColor }}">{{ substr($prestation->nom_artiste_groupe, 0, 1) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endfor
    </div>
</div>
