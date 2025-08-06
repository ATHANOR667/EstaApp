<div>
    {{-- In work, do what you enjoy. --}}

    <div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Mes Informations Personnelles</h2>

            @if ($admin)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Colonne de gauche: Infos Générales (Lecture seule) --}}
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Matricule --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                            <p class="form-display-text">{{ $matricule }}</p>
                        </div>

                        {{-- Nom --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                            <p class="form-display-text">{{ $nom }}</p>
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                            <p class="form-display-text">{{ $prenom ?: 'Non spécifié' }}</p>
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                            <p class="form-display-text">{{ $telephone ?: 'Non spécifié' }}</p>
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pays</label>
                            <p class="form-display-text">{{ $pays ?: 'Non spécifié' }}</p>
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ville</label>
                            <p class="form-display-text">{{ $ville ?: 'Non spécifiée' }}</p>
                        </div>

                        {{-- Statut du mot de passe (affichage) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de Passe</label>
                            <p class="form-display-text">{{ $admin->password ? 'Défini' : 'Non défini' }}</p>
                        </div>

                        {{-- Dernier changement de mot de passe --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernier changement mot de passe</label>
                            <p class="form-display-text">{{ $passwordChangedAt }}</p>
                        </div>

                        {{-- Statut du passcode (affichage) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Passcode</label>
                            <p class="form-display-text">{{ $admin->passcode ? 'Défini' : 'Non défini' }}</p>
                        </div>

                        {{-- Date de réinitialisation Passcode --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernière réinitialisation Passcode</label>
                            <p class="form-display-text">{{ $passcodeResetDate }}</p>
                        </div>
                    </div>

                    {{-- Colonne de droite: Photo de Profil, Pièces d'identité, QR Code (Lecture seule) --}}
                    <div class="md:col-span-1 space-y-6">
                        {{-- Photo de Profil --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de Profil</label>
                            <div class="flex items-center justify-center">
                                @if ($photoProfilUrl)
                                    <img src="{{ $photoProfilUrl }}" class="h-32 w-32 object-cover rounded-full shadow-md" alt="Photo de Profil">
                                @else
                                    <div class="h-32 w-32 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-5xl font-bold">
                                        {{ strtoupper(substr($prenom, 0, 1)) }}{{ strtoupper(substr($nom, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Pièces d'Identité (Recto/Verso) avec animation --}}
                        <div x-data="{ isFlipping: false }" class="relative perspective-1000">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pièce d'Identité</label>
                            <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                <div class="flip-card-inner w-full h-full relative"
                                     {{-- La rotation de l'élément interne est directement liée à l'état Livewire --}}
                                     :style="$wire.showPieceIdentiteRecto ? 'transform: rotateY(0deg)' : 'transform: rotateY(180deg)'"
                                     {{-- La classe de transition est appliquée/retirée par Alpine pour animer la rotation --}}
                                     :class="isFlipping ? 'transition-transform duration-600 ease-in-out' : ''">

                                    {{-- Recto : toujours présent dans le DOM, sa visibilité dépend de la rotation et backface-visibility --}}
                                    <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                        @if ($pieceIdentiteRectoUrl)
                                            <img src="{{ $pieceIdentiteRectoUrl }}" alt="Pièce d'Identité Recto"
                                                 class="object-contain max-h-full max-w-full cursor-pointer"
                                                 @click="
                                                isFlipping = true; // Active la transition CSS
                                                $wire.togglePieceIdentite(); // Change la propriété Livewire, ce qui inverse le transform: rotateY sur le parent
                                                setTimeout(() => {
                                                    isFlipping = false; // Désactive la transition après l'animation
                                                }, 600); // Doit correspondre à la durée de la transition
                                            ">
                                            <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto (cliquez pour Verso)</span>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400">Aucun Recto disponible</p>
                                        @endif
                                    </div>

                                    <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                        @if ($pieceIdentiteVersoUrl)
                                            <img src="{{ $pieceIdentiteVersoUrl }}" alt="Pièce d'Identité Verso"
                                                 class="object-contain max-h-full max-w-full cursor-pointer"
                                                 @click="
                                                isFlipping = true; // Active la transition CSS
                                                $wire.togglePieceIdentite(); // Change la propriété Livewire, ce qui inverse le transform: rotateY sur le parent
                                                setTimeout(() => {
                                                    isFlipping = false; // Désactive la transition après l'animation
                                                }, 600); // Doit correspondre à la durée de la transition
                                            ">
                                            <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso (cliquez pour Recto)</span>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400">Aucun Verso disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code QR de mon Matricule</label>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-inner flex items-center justify-center">
                                @if ($matricule)

                                    {!! QrCode::size(150)->generate($matricule) !!}
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">Matricule non disponible pour QR Code.</p>
                                @endif
                            </div>
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Matricule: {{ $matricule }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400">
                    Impossible de charger votre profil. Veuillez vous reconnecter.
                </div>
            @endif
        </div>
    </div>

    <style>
        .form-display-text {
            @apply px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm
            text-gray-800 dark:text-gray-200 font-medium break-words;
        }

        .perspective-1000 {
            perspective: 1000px;
        }
        .flip-card-inner {
            transform-style: preserve-3d;
        }
        .transition-transform {
            transition-property: transform;
        }
        .duration-600 {
            transition-duration: 600ms;
        }
        .ease-in-out {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        .backface-hidden {
            backface-visibility: hidden;
        }
        .flip-card-front {
            transform: rotateY(0deg);
            z-index: 2;
        }
        .flip-card-back {
            transform: rotateY(180deg);
            z-index: 1;
        }
    </style>

</div>
