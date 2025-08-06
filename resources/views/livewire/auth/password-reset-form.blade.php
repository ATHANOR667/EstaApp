
{{-- A good traveler has no fixed plans and is not intent upon arriving. --}}

<div x-data="{ step: @entangle('step').live }">
    {{-- Flash Message --}}
    @if (session()->has('password_reset_success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('password_reset_success') }}
        </div>
    @endif

    {{-- Step 1: Send OTP to known email --}}
    <div x-show="step === 1" x-transition>
        <p class="text-gray-700 dark:text-gray-300 mb-4">Pour changer votre mot de passe, nous enverrons un code de vérification à votre email enregistré ({{ Auth::guard($this->guard)->user()->email }}).</p>
        <form wire:submit.prevent="sendPasswordOtp">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-blue-600 transition-colors duration-200">
                Envoyer l'OTP
            </button>
            @error('otp') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
        </form>
    </div>

    {{-- Step 2: Enter OTP and new password --}}
    <div x-show="step === 2" x-transition>
        <p class="text-gray-700 dark:text-gray-300 mb-4">Un OTP a été envoyé à {{ Auth::guard($this->guard)->user()->email }}. Veuillez le saisir ci-dessous avec votre nouveau mot de passe.</p>
        <form wire:submit.prevent="processPasswordChange">
            <div class="mb-4">
                <label for="password_change_otp" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">OTP:</label>
                <input type="text" id="password_change_otp" name="otp" wire:model.live="otp"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required>
                @error('otp') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="new_password_password_change" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nouveau Mot de passe:</label>
                <input type="password" id="new_password_password_change" name="new_password" wire:model.live="newPassword"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="new-password">
                @error('newPassword') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="new_password_confirmation_password_change" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Confirmer le nouveau Mot de passe:</label>
                <input type="password" id="new_password_confirmation_password_change" name="new_password_confirmation" wire:model.live="newPasswordConfirmation"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-600 transition-colors duration-200">
                Changer le Mot de Passe
            </button>
            <button type="button" @click="step = 1; $wire.goBackToStep1()" class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">Retour</button>
        </form>
    </div>
</div>
