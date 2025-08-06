
{{-- Close your eyes. Count to one. That is how long forever feels. --}}
<div x-data="{ step: @entangle('step').live }">
    {{-- Flash Message --}}
    @if (session()->has('email_reset_success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('email_reset_success') }}
        </div>
    @endif

    {{-- Step 1: Request new email and current password --}}
    <div x-show="step === 1" x-transition>
        <form wire:submit.prevent="sendEmailOtp">
            <div class="mb-4">
                <label for="new_email_email_change" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nouvel Email:</label>
                <input type="email" id="new_email_email_change" name="new_email" wire:model.live="newEmail"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="email">
                @error('newEmail') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="current_password_email_change" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mot de passe actuel (pour confirmer):</label>
                <input type="password" id="current_password_email_change" name="current_password" wire:model.live="currentPassword"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="current-password">
                @error('currentPassword') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-600 transition-colors duration-200">
                Envoyer l'OTP au Nouvel Email
            </button>
        </form>
    </div>

    {{-- Step 2: Validate OTP for email change --}}
    <div x-show="step === 2" x-transition>
        <p class="text-gray-700 dark:text-gray-300 mb-4">Un OTP a été envoyé à votre nouvelle adresse email. Veuillez le saisir ci-dessous.</p>
        <form wire:submit.prevent="processEmailChange">
            <div class="mb-4">
                <label for="email_change_otp" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">OTP:</label>
                <input type="text" id="email_change_otp" name="otp" wire:model.live="otp"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required>
                @error('otp') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-600 transition-colors duration-200">
                Confirmer le changement d'Email
            </button>
            <button type="button" @click="step = 1; $wire.goBackToStep1()" class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">Retour</button>
        </form>
    </div>
</div>

