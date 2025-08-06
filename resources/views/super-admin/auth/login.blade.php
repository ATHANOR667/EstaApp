@extends('super-admin.disconnected-base')

@section('content')

    <div x-data="{ showLoginForm: true, showPasswordResetForm: false }"
         @@show-login-form.window="showLoginForm = true; showPasswordResetForm = false"
         class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300 ease-in-out">

        <div x-show="showLoginForm" x-transition
             class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-lg shadow-xl max-w-sm md:max-w-md w-full transition-colors duration-300 ease-in-out">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">Connexion</h2>
            <form action="" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mot de passe:</label>
                    <input type="password" id="password" name="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-400"
                           required autocomplete="current-password">
                    @error('password')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between mb-6">
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                       @click="showLoginForm = false; showPasswordResetForm = true">
                        Mot de passe oublié?
                    </a>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 dark:focus:ring-600 transition-colors duration-300 ease-in-out">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>

        {{-- Password Reset Form  --}}
        <div x-show="showPasswordResetForm" x-transition>
            <livewire:auth.password-reset-while-disconnected-form :guard="'super-admin'" />
        </div>
    </div>
@endsection
