{{-- In work, do what you enjoy. --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Informations du Compte</h2>

    <div class="mb-4">
        <p class="text-700 dark:text-gray-300">
            <strong class="font-medium">Compte créé le :</strong>
            {{ $user->created_at->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="mb-4">
        @if (is_null($user->email))
            <div class="bg-yellow-100 dark:bg-yellow-800 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-100 px-4 py-3 rounded relative shadow-md mb-4" role="alert">
                <strong class="font-bold">Attention !</strong>
                <span class="block sm:inline">Vous utilisez les identifiants par défaut. Veuillez changer votre email et mot de passe pour des raisons de sécurité.</span>
                <div class="mt-4">
                    {{-- This button will now DISPATCH an Alpine event --}}
                    <button
                        type="button"
                        @click="$dispatch('open-default-credentials-form')" {{-- <-- CHANGED THIS LINE --}}
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200"
                    >
                        Changer mes identifiants par défaut
                    </button>
                </div>
            </div>
        @else
            <p class="text-gray-700 dark:text-gray-300">
                <strong class="font-medium">Email actuel :</strong>
                {{ $user->email }}
            </p>
        @endif
    </div>

    <div class="mb-4">
        <p class="text-gray-700 dark:text-gray-300">
            <strong class="font-medium">Dernière modification du mot de passe :</strong>
            {{ $user->password_changed_at ? $user->password_changed_at->format('d/m/Y H:i') : 'Jamais modifié' }}
        </p>
    </div>
</div>
