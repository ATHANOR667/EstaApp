<div>
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-md transition-colors duration-300" style="background-color: rgba({{ $this->hex2rgb($artiste->couleur ?? '#f3f4f6') }}, 0.1);">
        @if ($artiste)
            <!-- Dynamic header with artist info -->
            <div class="flex flex-col items-center sm:flex-row sm:items-start gap-4 sm:gap-6 mb-6 sm:mb-8 p-4 sm:p-6 rounded-lg shadow-lg opacity-50"
                 style="background-color: {{ $artiste->couleur ?? '#f3f4f6' }};">
                <!-- Artist's profile picture or initial avatar -->
                @if($artiste->photo)
                    <img src="{{ asset('storage/' . $artiste->photo) }}"
                         class="h-16 w-16 sm:h-24 sm:w-24 rounded-full object-cover shadow-lg border-2 border-white dark:border-gray-800"
                         alt="Photo de profil de l'artiste">
                @else
                    <div class="h-16 w-16 sm:h-24 sm:w-24 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold text-2xl sm:text-4xl shadow-lg border-2 border-white dark:border-gray-800">
                        {{ strtoupper(substr($artiste->nom, 0, 1)) }}
                    </div>
                @endif

                <!-- Artist's name -->
                <div class="flex-1 text-center sm:text-left mt-2 sm:mt-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-white dark:text-gray-900 drop-shadow-lg">
                        Gérer l'équipe de {{ $artiste->nom }}
                    </h2>
                    <p class="text-white dark:text-gray-900 text-base sm:text-lg opacity-90">Administrateurs</p>
                </div>
            </div>

            <!-- Section to add new admins -->
            <div class="mb-6 sm:mb-8 border-b pb-6 sm:pb-8 border-gray-200 dark:border-gray-700">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Ajouter des administrateurs à l'équipe</h3>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 mb-4">
                    <input type="text" wire:model.live.debounce.300ms="searchAdmin" placeholder="Rechercher un administrateur..."
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                    <button wire:click="addAdminToTeam" wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Ajouter les sélectionnés
                    </button>
                </div>

                <div class="mt-4" wire:loading.class="opacity-50">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Administrateurs disponibles</h4>
                    @if ($availableAdmins->count())
                        <!-- Mobile: Card layout -->
                        <div class="space-y-4 sm:hidden">
                            @foreach ($availableAdmins as $admin)
                                <div wire:key="admin-{{ $admin->id }}" class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" wire:click="toggleAdminToAdd({{ $admin->id }})"
                                                   @if(in_array($admin->id, $adminsToAdd)) checked @endif
                                                   class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400">
                                            @if($admin->photo_profil)
                                                <img src="{{ asset('storage/' . $admin->photo_profil) }}" class="h-10 w-10 rounded-full object-cover" alt="Photo de profil">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold text-sm">
                                                    {{ strtoupper(substr($admin->prenom, 0, 1)) }}{{ strtoupper(substr($admin->nom, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $admin->prenom }} {{ $admin->nom }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop: Table layout -->
                        <div class="hidden sm:block max-h-80 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sélection</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Photo</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($availableAdmins as $admin)
                                    <tr wire:key="admin-{{ $admin->id }}" class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <input type="checkbox" wire:click="toggleAdminToAdd({{ $admin->id }})"
                                                   @if(in_array($admin->id, $adminsToAdd)) checked @endif
                                                   class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400">
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $admin->prenom }} {{ $admin->nom }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if($admin->photo_profil)
                                                <img src="{{ asset('storage/' . $admin->photo_profil) }}" class="h-10 w-10 rounded-full object-cover" alt="Photo de profil">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold text-sm">
                                                    {{ strtoupper(substr($admin->prenom, 0, 1)) }}{{ strtoupper(substr($admin->nom, 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $availableAdmins->links() }}
                        </div>
                    @else
                        <p class="text-center text-gray-500 dark:text-gray-400">Aucun administrateur trouvé.</p>
                    @endif
                </div>
            </div>

            <!-- Current team section -->
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 sm:mb-6">Équipe actuelle</h3>
                @if ($artiste->admins->count())
                    <!-- Mobile: Card layout -->
                    <div class="space-y-4 sm:hidden">
                        @foreach ($artiste->admins as $admin)
                            <div wire:key="team-admin-{{ $admin->id }}" class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        @if($admin->photo_profil)
                                            <img src="{{ asset('storage/' . $admin->photo_profil) }}" class="h-12 w-12 rounded-full object-cover" alt="Photo de profil">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold text-lg">
                                                {{ strtoupper(substr($admin->prenom, 0, 1)) }}{{ strtoupper(substr($admin->nom, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $admin->prenom }} {{ $admin->nom }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ $admin->matricule }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button wire:click="viewAdminDetails({{ $admin->id }})"
                                                class="px-3 py-1 text-xs text-blue-600 border border-blue-600 rounded-full hover:bg-blue-600 hover:text-white" aria-label="Voir le profil">
                                            Voir
                                        </button>
                                        <button wire:click.prevent="removeAdminFromTeam({{ $admin->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir retirer cet administrateur ?"
                                                class="px-3 py-1 text-xs text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white" aria-label="Retirer de l'équipe">
                                            Retirer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop: Grid layout -->
                    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($artiste->admins as $admin)
                            <div wire:key="team-admin-{{ $admin->id }}" class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 flex flex-col items-center text-center">
                                @if($admin->photo_profil)
                                    <img src="{{ asset('storage/' . $admin->photo_profil) }}" class="h-12 w-12 rounded-full object-cover mb-2" alt="Photo de profil">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold text-lg mb-2">
                                        {{ strtoupper(substr($admin->prenom, 0, 1)) }}{{ strtoupper(substr($admin->nom, 0, 1)) }}
                                    </div>
                                @endif
                                <p class="font-bold text-gray-900 dark:text-gray-100">{{ $admin->prenom }} {{ $admin->nom }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-300">{{ $admin->matricule }}</p>
                                <div class="mt-3 flex space-x-2">
                                    <button wire:click="viewAdminDetails({{ $admin->id }})"
                                            class="px-3 py-1 text-xs text-blue-600 border border-blue-600 rounded-full hover:bg-blue-600 hover:text-white" aria-label="Voir le profil">
                                        Voir
                                    </button>
                                    <button wire:click.prevent="removeAdminFromTeam({{ $admin->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir retirer cet administrateur ?"
                                            class="px-3 py-1 text-xs text-red-600 border border-red-600 rounded-full hover:bg-red-600 hover:text-white" aria-label="Retirer de l'équipe">
                                        Retirer
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400">Aucun administrateur n'est encore dans l'équipe de cet artiste.</p>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center justify-center p-6 sm:p-10 text-center text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A12.003 12.003 0 0112.015 5.865a12.003 12.003 0 016.892 11.956m-9.987-1.163A6.002 6.002 0 0112.005 5.14a6.002 6.002 0 015.892 11.492" />
                </svg>
                <p class="text-base sm:text-lg font-semibold">Veuillez sélectionner un artiste pour gérer son équipe.</p>
            </div>
        @endif
    </div>

    <style>
        /* Ensure text is always visible on the dynamic background */
        .dark .drop-shadow-lg {
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        /* Custom scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #4a5568;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #64748b;
            border: 2px solid #4a5568;
        }
    </style>
</div>
