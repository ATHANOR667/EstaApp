<div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg border border-blue-100 dark:border-blue-700">

    <h3 class="text-3xl font-extrabold text-blue-900 dark:text-blue-100 mb-8 flex items-center">
        <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"/>
        </svg>
        Statistiques des Rôles & Permissions
        <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200 text-sm font-semibold rounded-full shadow-inner">
            Guard: {{ $guardName }}
        </span>
    </h3>

    {{-- Section des Catégories de Permissions --}}
    <div class="mb-8 border-b pb-6 border-blue-200 dark:border-blue-700">
        <h4 class="text-xl font-semibold text-blue-700 dark:text-blue-200 mb-4">Catégories de Permissions</h4>
        <div class="flex flex-wrap gap-4 text-sm">
            {{-- Dynamiquement générer les catégories de permissions --}}
            @if(!empty($permissionCategories))
                @foreach($permissionCategories as $categoryName)
                    <div class="flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-800 rounded-full text-blue-700 dark:text-blue-300 shadow-sm">
                        <span class="font-semibold capitalize">{{ str_replace('_', ' ', $categoryName) }}</span>
                    </div>
                @endforeach
            @else
                <p class="text-blue-500 dark:text-blue-400">Aucune catégorie de permission définie.</p>
            @endif
        </div>
    </div>

    {{-- Stats sur les Permissions: Regroupement par catégorie avec titres --}}
    <div class="mb-10 pt-6 border-b pb-6 border-blue-200 dark:border-blue-700">
        <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-100 mb-6 flex items-center">
            <svg class="h-6 w-6 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.7-7 8.94V12H5V6.3l7-3.11v8.79z"/>
            </svg>
            Permissions Attribuées aux Rôles
        </h4>

        @if(empty($permissionRoleCounts))
            <div class="bg-blue-50 dark:bg-blue-700 p-6 rounded-lg text-center text-blue-500 dark:text-blue-400">
                <p class="mb-2">Aucune statistique de permission disponible pour le guard '{{ $guardName }}'.</p>
                <p>Pour voir des données ici, veuillez créer des permissions et les attacher à des rôles.</p>
            </div>
        @else
            @php
                $permissionsGroupedByCategory = collect($permissionRoleCounts)->groupBy('categorie');
            @endphp

            @foreach($permissionsGroupedByCategory as $category => $permissionsInGroup)
                <div class="mb-8">
                    <h5 class="text-xl font-semibold text-blue-700 dark:text-blue-300 mb-4 border-b border-blue-200 dark:border-blue-600 pb-2">
                        Catégorie: <span class="capitalize">{{ str_replace('_', ' ', $category ?: 'Non Catégorisée') }}</span>
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($permissionsInGroup as $permissionId => $data)
                            @php
                                $percentage = $allRolesCount > 0 ? round(($data['count'] / $allRolesCount) * 100) : 0;
                            @endphp
                            <div class="bg-blue-50 dark:bg-blue-700 p-5 rounded-lg shadow-sm border border-blue-300 dark:border-blue-600 hover:shadow-lg transition-shadow duration-200">
                                <h6 class="font-bold text-lg mb-2 text-blue-800 dark:text-blue-100">
                                    {{ $data['name'] }}
                                </h6>
                                <div class="mb-3">
                                    <p class="text-blue-700 dark:text-blue-200 text-sm">
                                        Attribuée à <span class="font-bold">{{ $data['count'] }}</span> rôle(s)
                                        @if($allRolesCount > 0)
                                            sur {{ $allRolesCount }} rôles
                                        @endif
                                        :
                                    </p>
                                    <div class="w-full bg-blue-200 rounded-full h-2.5 dark:bg-blue-600 mt-2">
                                        <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <p class="text-right text-xs text-blue-500 dark:text-blue-400 mt-1">{{ $percentage }}%</p>
                                </div>

                                @if ($data['count'] > 0)
                                    <h6 class="font-medium text-blue-700 dark:text-blue-300 mb-1">Rôles associés :</h6>
                                    <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 text-sm space-y-0.5 max-h-20 overflow-y-auto custom-scrollbar pr-2">
                                        @foreach ($data['roles'] as $roleName)
                                            <li>{{ $roleName }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-blue-500 dark:text-blue-400 text-sm mt-1">Aucun rôle ne dispose de cette permission.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Stats sur les Rôles --}}
    <div class="pt-6">
        <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-100 mb-6 flex items-center">
            <svg class="h-6 w-6 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            Rôles Attribués aux Utilisateurs
        </h4>
        @if(empty($roleUserCounts))
            <div class="bg-blue-50 dark:bg-blue-700 p-6 rounded-lg text-center text-blue-500 dark:text-blue-400">
                <p class="mb-2">Aucune statistique de rôle disponible pour le guard '{{ $guardName }}'.</p>
                <p>Pour voir des données ici, veuillez créer des rôles et les attribuer à des utilisateurs.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @php
                    $totalUsersWithRoles = $userModelClass::all()->count() ;
                @endphp
                @foreach ($roleUserCounts as $roleId => $data)
                    <div class="bg-blue-50 dark:bg-blue-700 p-5 rounded-lg shadow-sm border border-blue-300 dark:border-blue-600 hover:shadow-lg transition-shadow duration-200">
                        <h5 class="font-bold text-lg mb-2 text-blue-800 dark:text-blue-100">{{ $data['name'] }}</h5>
                        <p class="text-blue-700 dark:text-blue-200 text-sm mb-3">
                            Attribué à <span class="font-bold">{{ $data['count'] }}</span> utilisateur(s) :
                        </p>
                        @php
                            $percentageUsers = $totalUsersWithRoles > 0 ? round(($data['count'] / $totalUsersWithRoles) * 100) : 0;
                        @endphp
                        <div class="w-full bg-blue-200 rounded-full h-2.5 dark:bg-blue-600 mt-2">
                            <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ $percentageUsers }}%"></div>
                        </div>
                        <p class="text-right text-xs text-blue-500 dark:text-blue-400 mt-1">{{ $percentageUsers }}% des utilisateurs avec rôles</p>

                        @if ($data['count'] > 0)
                            <h6 class="font-medium text-blue-700 dark:text-blue-300 mb-1">Utilisateurs associés :</h6>
                            <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 text-sm space-y-0.5 max-h-20 overflow-y-auto custom-scrollbar pr-2">
                                @foreach ($data['users'] as $userName)
                                    <li>{{ $userName }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-blue-500 dark:text-blue-400 text-sm mt-1">Aucun utilisateur ne dispose de ce rôle.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #e0f2f7;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #90caf9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64b5f6;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #90caf9 #e0f2f7;
        }
    </style>
</div>
