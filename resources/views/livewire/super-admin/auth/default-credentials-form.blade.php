<div x-data="{ currentStep: 1 }" {{-- Alpine gère 'currentStep' --}}
@set-default-credentials-step.window="currentStep = $event.detail.step" {{-- Écoute l'événement Livewire pour changer d'étape --}}
     class="relative">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Configuration des Identifiants Initiaux</h2>

        {{-- Close Button --}}
        {{-- Envoie l'événement au parent Alpine pour cacher le formulaire --}}
        <button type="button" @click="$dispatch('hide-default-credentials-form'); currentStep = 1;" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Flash Messages and Errors (using @error is fine in Livewire Blade views) --}}
        @if (session()->has('success'))
            <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @error('initialEmail')
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ $message }}
        </div>
        @enderror
        @error('initialOtp')
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ $message }}
        </div>
        @enderror
        @error('initialPassword')
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ $message }}
        </div>
        @enderror

        {{-- Step 1: Request Email and Send OTP --}}
        <div x-show="currentStep === 1" x-transition>
            <form wire:submit.prevent="sendOtp"> {{-- Livewire soumet le formulaire --}}
                <div class="mb-4">
                    <label for="initial_email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Votre Email (pour l'OTP):</label>
                    <input type="email" id="initial_email" name="initial_email" wire:model.live="initialEmail"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="email">
                </div>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-600 transition-colors duration-200">
                    Envoyer l'OTP
                </button>
            </form>
        </div>

        {{-- Step 2: Validate OTP and Set Password --}}
        <div x-show="currentStep === 2" x-transition>
            <p class="text-gray-700 dark:text-gray-300 mb-4">Un OTP a été envoyé à l'adresse email fournie. Veuillez le saisir ci-dessous.</p>
            <form wire:submit.prevent="processCredentials"> {{-- Livewire soumet le formulaire --}}
                <div class="mb-4">
                    <label for="initial_otp" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">OTP:</label>
                    <input type="text" id="initial_otp" name="initial_otp" wire:model.live="initialOtp"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required>
                </div>
                <div class="mb-4">
                    <label for="initial_password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nouveau Mot de passe:</label>
                    <input type="password" id="initial_password" name="initial_password" wire:model.live="initialPassword"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="new-password">
                </div>
                <div class="mb-6">
                    <label for="initial_password_confirmation" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Confirmer le nouveau Mot de passe:</label>
                    <input type="password" id="initial_password_confirmation" name="initial_password_confirmation" wire:model.live="initialPasswordConfirmation"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="new-password">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-600 transition-colors duration-200">
                    Valider et Enregistrer
                </button>
                {{-- Bouton Retour : Alpine gère le changement d'étape --}}
                <button type="button" @click="currentStep = 1; $wire.goBackToStep1()" class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">Retour</button>
            </form>
        </div>
    </div>
</div>
