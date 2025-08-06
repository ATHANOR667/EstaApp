<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', sidebarOpen: false }"
      :class="{'dark': darkMode}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super-admin Panel</title>



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

    <title>@yield('title')</title>
</head>
<body class="h-full bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300 ease-in-out flex flex-col min-h-screen font-sans antialiased">

<nav class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300 ease-in-out">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{route('super-admin.manageAdminsView')}}" class="text-2xl font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
            Super-Admin
        </a>

        <div class="md:hidden">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 focus:ring-blue-500">
                <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="hidden md:flex items-center space-x-6">
            <a href="{{route('super-admin.manageAdminsView')}}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Admins</a>
            <a href="{{route('super-admin.profileView')}}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Profil</a>

            <form action="{{route('super-admin.auth.connected.logout')}}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    Déconnexion
                </button>
            </form>

            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                    class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 focus:ring-blue-500 transition-colors duration-300 ease-in-out"
                    aria-label="Toggle dark mode">
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.325-7.757l-.707.707M6.343 17.657l-.707.707M16.95 7.05l.707-.707M7.05 16.95l-.707.707M12 15a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>
    </div>
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden px-4 py-2 space-y-2 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <a href="{{route('super-admin.manageAdminsView')}}" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 py-1">Admins</a>
        <a href="{{route('super-admin.profileView')}}" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 py-1">Profil</a>
        <form action="{{route('super-admin.auth.connected.logout')}}" method="POST" class="pt-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="block w-full text-left px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                Déconnexion
            </button>
        </form>
        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                    class="w-full flex items-center justify-center p-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 focus:ring-blue-500 transition-colors duration-300 ease-in-out">
                <svg x-show="!darkMode" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M4 12H3m15.325-7.757l-.707.707M6.343 17.657l-.707.707M16.95 7.05l.707-.707M7.05 16.95l-.707.707M12 15a3 3 0 100-6 3 3 0 000 6z" /></svg>
                <svg x-show="darkMode" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <span x-text="darkMode ? 'Mode Clair' : 'Mode Sombre'"></span>
            </button>
        </div>
    </div>
</nav>

<main class="flex-grow container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto mb-6">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave.opacity.duration.500ms
                 class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between mb-4" role="alert">
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
                 class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between mb-4" role="alert">
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
                 class="bg-blue-100 dark:bg-blue-800 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-100 px-4 py-3 rounded relative shadow-md flex items-center justify-between mb-4" role="alert">
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

<footer class="bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 p-4 text-center shadow-inner mt-auto transition-colors duration-300 ease-in-out">
    <div class="container mx-auto">
        <p class="text-sm md:text-base">&copy; {{ date('Y') }} Super-admin Panel. Tous droits réservés.</p>
    </div>
</footer>
</body>
</html>
