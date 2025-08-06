<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap)');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl text-center max-w-xl">
    <div class="mb-4">
        @if (session('success'))
            <div class="text-green-500">
                <svg class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold mt-4">Opération réussie !</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ session('success') }}</p>
        @else
            <div class="text-red-500">
                <svg class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold mt-4">Erreur</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ session('error') }}</p>
        @endif
    </div>
    <p class="mt-4 text-sm text-gray-500 dark:text-gray-500">
        Vous pouvez fermer cette fenêtre.
    </p>
</div>
</body>
</html>
