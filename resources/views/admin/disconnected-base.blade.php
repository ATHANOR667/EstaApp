<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      :class="{'dark': darkMode}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Authentication</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {

                },
            }
        }
    </script>
    @livewireStyles
    @livewireScripts

</head>
<body class="h-full bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300 ease-in-out flex flex-col min-h-screen font-sans antialiased">
<header class="bg-blue-600 dark:bg-blue-800 text-white p-4 shadow-md transition-colors duration-300 ease-in-out">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl font-extrabold text-center flex-grow">Admin Panel Authentication</h1>
        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                class="p-2 rounded-full bg-blue-700 dark:bg-blue-900 text-white hover:bg-blue-800 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 dark:focus:ring-offset-blue-800 focus:ring-white transition-colors duration-300 ease-in-out"
                aria-label="Toggle dark mode">
            <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.325-7.757l-.707.707M6.343 17.657l-.707.707M16.95 7.05l.707-.707M7.05 16.95l-.707.707M12 15a3 3 0 100-6 3 3 0 000 6z" />
            </svg>
            <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>
</header>

<main class="flex-grow container mx-auto p-4 flex flex-col items-center justify-center">
    <div class="w-full max-w-sm md:max-w-md mb-6">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave.opacity.duration.500ms
                 class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button @click="show = false" class="ml-4 text-green-700 dark:text-green-100 hover:text-green-900 dark:hover:text-green-300 focus:outline-none">
                    <svg class="h-5 w-5" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave.opacity.duration.500ms
                 class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button @click="show = false" class="ml-4 text-red-700 dark:text-red-100 hover:text-red-900 dark:hover:text-red-300 focus:outline-none">
                    <svg class="h-5 w-5" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('message'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave.opacity.duration.500ms
                 class="bg-blue-100 dark:bg-blue-800 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button @click="show = false" class="ml-4 text-blue-700 dark:text-blue-100 hover:text-blue-900 dark:hover:text-blue-300 focus:outline-none">
                    <svg class="h-5 w-5" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

    @yield('content')
</main>

<footer class="bg-gray-800 dark:bg-gray-900 text-white p-4 text-center shadow-inner mt-auto transition-colors duration-300 ease-in-out">
    <div class="container mx-auto">
        <p class="text-sm md:text-base">&copy; {{ date('Y') }} Admin Panel. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
