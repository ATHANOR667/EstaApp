<div
    x-data="{ showModal: @entangle('showModal').live, isViewing: @entangle('isViewing').live, contratId: @entangle('contratId').live, showSendOptions: @entangle('showSendOptions').live, isGenerating: @entangle('isGenerating').live }"
    x-show="showModal"
    x-on:keydown.escape.window="$wire.closeModal()"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    @once
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <style>
            .dark .ql-editor {
                color: #e5e7eb;
            }
        </style>
    @endonce

    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>

    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative z-50 flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative w-full transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl dark:bg-gray-800" @click.outside="$wire.closeModal()">
            <form wire:submit.prevent="saveContrat" class="flex flex-col h-[80vh] overflow-hidden">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 flex-shrink-0">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                <span x-text="isViewing ? 'Visualiser le contrat' : (contratId ? 'Modifier le contrat' : 'Créer un contrat')"></span>
                            </h3>
                            <div x-show="isGenerating" class="mt-2 text-blue-600 font-bold flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Génération du contrat en cours...
                            </div>
                            <div x-show="$wire.get('session.error')" class="mt-2 p-2 text-red-700 bg-red-100 rounded-lg dark:text-red-200 dark:bg-red-800" x-text="$wire.get('session.error')"></div>
                            <div x-show="$wire.get('session.success')" class="mt-2 p-2 text-green-700 bg-green-100 rounded-lg dark:text-green-200 dark:bg-green-800" x-text="$wire.get('session.success')"></div>
                        </div>
                    </div>
                    <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                        <button type="button" @click="$wire.closeModal()" class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-grow overflow-y-auto px-4 sm:px-6 mb-4">
                    <div
                        wire:ignore
                        x-data="{
                            editor: null,
                            init() {
                                this.editor = new Quill(this.$refs.quillEditor, {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            ['blockquote', 'code-block'],
                                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                            [{ 'color': [] }, { 'background': [] }],
                                            ['clean']
                                        ]
                                    }
                                });

                                this.editor.on('text-change', () => {
                                    this.$wire.set('form.content', this.editor.root.innerHTML);
                                });

                                this.$watch('$wire.form.content', (newContent, oldContent) => {
                                    if (newContent !== oldContent && newContent !== this.editor.root.innerHTML) {
                                        this.editor.root.innerHTML = newContent;
                                    }
                                });

                                Livewire.on('reset-quill', () => {
                                    this.editor.root.innerHTML = '';
                                });

                                this.$watch('isViewing', value => this.editor.enable(!value && !this.isGenerating));
                                this.$watch('isGenerating', value => this.editor.enable(!value && !this.isViewing));

                                this.editor.root.innerHTML = this.$wire.get('form.content');
                            }
                        }"
                    >
                        <div x-ref="quillEditor" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg min-h-[300px]"></div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 flex-shrink-0">
                    <div x-show="!isViewing || showSendOptions" class="sm:flex">
                        <div x-show="showSendOptions" class="sm:flex space-x-3">
                            <button
                                type="button"
                                @click="$wire.sendByMail()"
                                :disabled="isGenerating"
                                aria-label="Envoyer le contrat par email"
                                class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto"
                            >
                                Envoyer par Email
                            </button>
                            <button
                                type="button"
                                @click="$wire.sendBySMS()"
                                :disabled="isGenerating"
                                aria-label="Envoyer le contrat par SMS"
                                class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto"
                            >
                                Envoyer par SMS
                            </button>
                            <button
                                type="button"
                                @click="$wire.sendByWhatsApp()"
                                :disabled="isGenerating"
                                aria-label="Envoyer le contrat par WhatsApp"
                                class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto"
                            >
                                Envoyer par WhatsApp
                            </button>
                            <button
                                type="button"
                                @click="$wire.closeModal()"
                                aria-label="Annuler l'envoi"
                                class="inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:w-auto"
                            >
                                Annuler
                            </button>
                        </div>

                        <div x-show="!showSendOptions" class="sm:flex space-x-3">
                            <button
                                type="button"
                                @click="$wire.generateContent()"
                                x-show="!contratId"
                                :disabled="isGenerating"
                                aria-label="Générer le contrat avec l'IA"
                                class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:w-auto"
                            >
                                Générer avec IA
                            </button>

                            <button
                                type="button"
                                @click="$wire.saveAndSend()"
                                :disabled="isGenerating || !$wire.form.content"
                                aria-label="Sauvegarder et envoyer le contrat"
                                class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto"
                            >
                                Sauvegarder & Envoyer
                            </button>

                            <button
                                type="button"
                                @click="$wire.saveContrat()"
                                :disabled="isGenerating || !$wire.form.content"
                                aria-label="Sauvegarder le contrat"
                                class="inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:w-auto"
                            >
                                Sauvegarder
                            </button>
                        </div>
                    </div>

                    <button
                        x-show="isViewing && !showSendOptions"
                        @click="$wire.deleteContrat()"
                        onclick="confirm('Supprimer ce contrat ?') || event.stopImmediatePropagation()"
                        class="px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600"
                    >
                        Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
