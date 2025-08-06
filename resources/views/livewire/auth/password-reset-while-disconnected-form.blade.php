{{-- The whole world belongs to you. --}}
<div x-data="{ currentStep: 1 }"
     @set-password-reset-step.window="currentStep = $event.detail.step" {{-- Listen for Livewire to change step --}}
     class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-xl max-w-sm md:max-w-md w-full transition-colors duration-300 ease-in-out relative">
    <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">Réinitialiser le Mot de Passe</h2>

    {{-- Close Button --}}
    <button type="button" @click="$wire.cancelReset()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Flash Message --}}
    @if (session()->has('password_reset_success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('password_reset_success') }}
        </div>
    @endif

    {{-- Step 1: Enter Email and Send OTP --}}
    <div x-show="currentStep === 1" x-transition>
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-center">Entrez votre adresse email pour recevoir un code de vérification.</p>
        <form wire:submit.prevent="sendOtp">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" wire:model.live="email"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="email">
                @error('email') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-600 transition-colors duration-200">
                    Envoyer le Code
                </button>
            </div>
            <div class="text-center mt-4">
                <button type="button" @click="$wire.cancelReset()" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Annuler et Retour à la Connexion
                </button>
            </div>
        </form>
    </div>

    {{-- Step 2: Enter OTP and New Password --}}
    <div x-show="currentStep === 2" x-transition>
        <p class="text-gray-700 dark:text-gray-300 mb-4 text-center">Veuillez entrer le code OTP reçu et votre nouveau mot de passe.</p>
        <form wire:submit.prevent="resetPassword">
            <div class="mb-4">
                <label for="otp" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Code OTP:</label>
                <input type="text" id="otp" name="otp" wire:model.live="otp"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required>
                @error('otp') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nouveau Mot de passe:</label>
                <input type="password" id="password" name="password" wire:model.live="password"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="new-password">
                @error('password') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Confirmer le nouveau Mot de passe:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" wire:model.live="passwordConfirmation"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="new-password">
                @error('passwordConfirmation') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-600 transition-colors duration-200">
                    Réinitialiser le Mot de Passe
                </button>
            </div>
            <div class="text-center mt-4">
                <button type="button" @click="currentStep = 1; $wire.goBackToStep1()" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Retour
                </button>
            </div>
        </form>
    </div>
</div>


