<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-100 dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat de Prestation - Validation</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .quill-editor {
            border: 1px solid #e2e8f0; /* gray-200 */
            border-radius: 0.5rem; /* rounded-lg */
            background-color: #f8fafc; /* gray-50 */
            padding: 1.5rem; /* p-6 */
        }
        .dark .quill-editor {
            border-color: #4a5568; /* gray-700 */
            background-color: #1a202c; /* gray-900 */
            color: #e2e8f0; /* gray-200 */
        }
        .quill-editor .ql-container.ql-snow {
            border: none;
        }
        .quill-editor .ql-toolbar.ql-snow {
            display: none; /* Cache la barre d'outils */
        }
        .ql-editor {
            min-height: 400px;
        }
    </style>

    <!-- Alpine.js CDN -->
    <script src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
    <!-- Quill.js CDN -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
</head>
<body class="h-full flex items-center justify-center p-4">
<div x-data="{
        showApproveModal: false,
        showRejectModal: false,
        signatureText: '',
        requiredApproveText: 'Lu et approuve',
        requiredRejectText: 'Rejeter',
        canApprove: false,
        canReject: false,
        init() {
            this.$watch('signatureText', value => {
                this.canApprove = value.toLowerCase().trim() === this.requiredApproveText.toLowerCase().trim();
                this.canReject = value.toLowerCase().trim() === this.requiredRejectText.toLowerCase().trim();
            });
            // Initialisation de l'éditeur Quill en mode lecture seule
            var quill = new Quill('#editor-container', {
                readOnly: true,
                theme: 'snow',
                modules: {
                    toolbar: false // Cache explicitement la barre d'outils
                }
            });
            // Définit le contenu
            quill.root.innerHTML = document.getElementById('contract-content').innerHTML;
        }
    }" class="w-full max-w-5xl mx-auto my-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8">

    <header class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Validation du Contrat de Prestation</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Veuillez lire attentivement le contenu du contrat ci-dessous.
            Il a déjà été validé par le représentant de l'artiste.
        </p>
    </header>

    <!-- Affichage du contenu du contrat avec Quill.js en mode lecture seule -->
    <div id="editor-container" class="quill-editor"></div>
    <div id="contract-content" class="hidden">{!! $contrat->content !!}</div>

    <form id="validation-form" {{--action="{{ route('contrats.validate_signed', ['contrat' => $contrat->id, 'expires' => request()->query('expires'), 'signature' => request()->query('signature')]) }}"--}} method="POST" class="mt-8">

        @csrf

        <input type="hidden" name="action" id="action-input">
        <input type="hidden" name="signature_text" id="signature-text-input">

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <button type="button" @click="showApproveModal = true; signatureText='';" class="w-full sm:w-auto px-6 py-3 font-semibold text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Approuver
            </button>
            <button type="button" @click="showRejectModal = true; signatureText='';" class="w-full sm:w-auto px-6 py-3 font-semibold text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Rejeter
            </button>
        </div>
    </form>

    <!-- Modal pour l'approbation -->
    <div x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div @click="showApproveModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Confirmer l'approbation
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pour valider ce contrat, veuillez taper la phrase exacte **"Lu et approuve"** dans le champ ci-dessous.
                        </p>
                        <input type="text" x-model="signatureText" class="mt-4 w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200" placeholder="Écrivez 'Lu et approuve'">
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="document.getElementById('action-input').value = 'approve'; document.getElementById('signature-text-input').value = signatureText; document.getElementById('validation-form').submit();" :disabled="!canApprove" class="w-full sm:ml-3 sm:w-auto px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirmer
                    </button>
                    <button type="button" @click="showApproveModal = false; signatureText = ''; canApprove = false" class="mt-3 w-full sm:mt-0 sm:w-auto px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-md shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour le rejet -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div @click="showRejectModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Confirmer le rejet
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pour rejeter ce contrat, veuillez taper la phrase exacte **"Rejeter"** dans le champ ci-dessous.
                        </p>
                        <input type="text" x-model="signatureText" class="mt-4 w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200" placeholder="Écrivez 'Rejeter'">
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="document.getElementById('action-input').value = 'reject'; document.getElementById('signature-text-input').value = signatureText; document.getElementById('validation-form').submit();" :disabled="!canReject" class="w-full sm:ml-3 sm:w-auto px-4 py-2 bg-red-600 text-white font-semibold rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirmer le rejet
                    </button>
                    <button type="button" @click="showRejectModal = false; signatureText = ''; canReject = false" class="mt-3 w-full sm:mt-0 sm:w-auto px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-md shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
