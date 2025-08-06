<div> @php use Carbon\Carbon ;@endphp

    <div class="flex-grow p-4">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Prestations du {{ $currentDate->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </h3>

        {{-- Liste des prestations pour le jour sélectionné --}}
        @forelse ($prestations as $prestation)
            <div wire:click="openPrestationDetails({{ $prestation->id }})"
                 class="bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if ($prestation->artiste && $prestation->artiste->photo)
                        {{-- Photo de l'artiste --}}
                        <img src="{{ $prestation->artiste->photo }}" alt="{{ $prestation->artiste->nom }}"
                             class="w-12 h-12 rounded-full object-cover border-2"
                             style="border-color: {{ $prestation->artiste->couleur ?? '#cbd5e0' }};">
                    @else
                        {{-- Initiale de l'artiste si pas de photo, avec couleur de fond et texte dynamique --}}
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold
                                bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200 border-2"
                             style="background-color: {{ $prestation->artiste->couleur ?? '#cbd5e0' }}; color: {{ $prestation->artiste->couleur ? LightenDarkenColor($prestation->artiste->couleur, -80) : '#1f2937' }}; border-color: {{ $prestation->artiste->couleur ?? '#cbd5e0' }};">
                            {{ substr($prestation->nom_artiste_groupe, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    {{-- Nom de l'artiste/groupe avec couleur dynamique --}}
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                       @if ($prestation->artiste && $prestation->artiste->couleur)
                           style="color: {{ $prestation->artiste->couleur }};"
                        @endif>
                        {{ $prestation->nom_artiste_groupe }}
                    </p>
                    {{-- Heures et lieu --}}
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ Carbon::parse($prestation->heure_debut_prestation)->format('H:i') }} - {{ Carbon::parse($prestation->heure_fin_prevue)->format('H:i') }} | {{ $prestation->lieu_prestation }}
                    </p>
                    {{-- Type d'événement --}}
                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                        Type: {{ $prestation->type_evenement }}
                    </p>
                    {{-- Statut de la prestation avec couleur --}}
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        Statut: <span class="font-medium
                        {{ $prestation->status == 'redigee' ? 'text-blue-600' : '' }}
                        {{ $prestation->status == 'en cours de redaction' ? 'text-purple-600' : '' }}
                        {{ $prestation->status == 'annulee' ? 'text-red-600' : '' }}
                        {{ $prestation->status == 'terminee' ? 'text-green-600' : '' }}">
                        {{ ucfirst($prestation->status) }}
                    </span>
                    </p>
                </div>
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-300 text-center py-8">
                Aucune prestation prévue pour cette journée.
            </p>
        @endforelse
    </div>

    {{-- JavaScript pour éclaircir/assombrir une couleur (utilisé pour le texte sur le cercle de couleur de l'artiste) --}}
    {{-- Cette fonction est utile pour s'assurer que le texte est lisible sur la couleur de fond de l'artiste --}}
    <script>
        function LightenDarkenColor(col, amt) {
            var usePound = false;
            if (col[0] == "#") {
                col = col.slice(1);
                usePound = true;
            }
            var num = parseInt(col, 16);
            var r = (num >> 16) + amt;
            if (r > 255) r = 255;
            else if (r < 0) r = 0;
            var b = ((num >> 8) & 0x00FF) + amt;
            if (b > 255) b = 255;
            else if (b < 0) b = 0;
            var g = (num & 0x0000FF) + amt;
            if (g > 255) g = 255;
            else if (g < 0) g = 0;
            return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
        }
    </script>


</div>
