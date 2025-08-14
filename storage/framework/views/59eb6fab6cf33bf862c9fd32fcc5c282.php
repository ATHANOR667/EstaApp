<div
    x-data="{ showModal: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?>.live, errorOccurred: <?php if ((object) ('errorOccurred') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('errorOccurred'->value()); ?>')<?php echo e('errorOccurred'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('errorOccurred'); ?>')<?php endif; ?>.live, method: <?php if ((object) ('method') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('method'->value()); ?>')<?php echo e('method'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('method'); ?>')<?php endif; ?>.live }"
    x-show="showModal"
    x-on:keydown.escape.window="$wire.closeModal()"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>

    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>

    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative z-50 flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative w-full transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:bg-gray-800" @click.outside="$wire.closeModal()">
            <form class="flex flex-col p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                            Envoyer le contrat via <span x-text="method.charAt(0).toUpperCase() + method.slice(1)"></span>
                        </h3>

                        
                        <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
                            <div class="mt-2 p-2 text-green-700 bg-green-100 rounded-lg dark:text-green-200 dark:bg-green-800">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!--[if BLOCK]><![endif]--><?php if(session('warning')): ?>
                            <div class="mt-2 p-2 text-yellow-700 bg-yellow-100 rounded-lg dark:text-yellow-200 dark:bg-yellow-800">
                                <?php echo e(session('warning')); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <div x-show="errorOccurred" class="mt-2 p-2 text-red-700 bg-red-100 rounded-lg dark:text-red-200 dark:bg-red-800" x-text="$wire.errorMessage"></div>
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

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="artiste_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de l’artiste</label>
                        <input
                            type="text"
                            id="artiste_name"
                            wire:model="form.artiste_name"
                            disabled
                            class="block w-full rounded-md border-0 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 py-2 px-3 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            aria-describedby="artiste_name_description"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="artiste_name_description">Nom de l’artiste (non modifiable).</p>
                    </div>
                    <div>
                        <label for="artiste_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email de l’artiste</label>
                        <input
                            type="email"
                            id="artiste_contact"
                            wire:model="form.artiste_contact"
                            disabled
                            class="block w-full rounded-md border-0 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 py-2 px-3 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            aria-describedby="artiste_contact_description"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="artiste_contact_description">Email de l’artiste (non modifiable).</p>
                    </div>
                    <div>
                        <label for="contractant_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du contractant</label>
                        <input
                            type="text"
                            id="contractant_name"
                            wire:model="form.contractant_name"
                            class="block w-full rounded-md border-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 py-2 px-3 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            aria-describedby="contractant_name_error"
                        />
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.contractant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="mt-1 text-xs text-red-600 dark:text-red-400 flex items-center" id="contractant_name_error">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?php echo e($message); ?>

                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label for="contractant_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="method === 'email' ? 'Email du contractant' : 'Numéro du contractant'"></label>
                        <input
                            type="text"
                            id="contractant_contact"
                            wire:model="form.contractant_contact"
                            class="block w-full rounded-md border-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 py-2 px-3 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            :class="{'ring-red-500 dark:ring-red-400': $wire.get('errors.form.contractant_contact')}"
                            aria-describedby="contractant_contact_error"
                        />
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.contractant_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="mt-1 text-xs text-red-600 dark:text-red-400 flex items-center" id="contractant_contact_error">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?php echo e($message); ?>

                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 mt-6 gap-3">
                    
                    <div wire:loading.remove class="flex flex-col sm:flex-row sm:flex-1 space-y-3 sm:space-y-0 sm:space-x-3 sm:justify-end">
                        <button
                            type="button"
                            @click="$wire.call('sendBy' + (method === 'email' ? 'Mail' : (method === 'sms' ? 'SMS' : 'WhatsApp')))"
                            class="inline-flex justify-center items-center w-full sm:w-auto rounded-md bg-green-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            :disabled="errorOccurred && method === 'email'"
                        >
                            Envoyer
                        </button>
                        <div x-show="errorOccurred" class="sm:flex sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 mt-3 sm:mt-0">
                            <button
                                type="button"
                                @click="method = 'email'; $wire.set('method', 'email')"
                                class="inline-flex justify-center items-center w-full sm:w-auto rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            >
                                Essayer par Email
                            </button>
                            <button
                                type="button"
                                @click="method = 'sms'; $wire.set('method', 'sms')"
                                class="inline-flex justify-center items-center w-full sm:w-auto rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            >
                                Essayer par SMS
                            </button>
                            <button
                                type="button"
                                @click="method = 'whatsapp'; $wire.set('method', 'whatsapp')"
                                class="inline-flex justify-center items-center w-full sm:w-auto rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                            >
                                Essayer par WhatsApp
                            </button>
                        </div>
                        <button
                            type="button"
                            @click="$wire.closeModal()"
                            class="mt-3 sm:mt-0 inline-flex justify-center items-center w-full sm:w-auto rounded-md bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200"
                        >
                            Annuler
                        </button>
                    </div>

                    
                    <div wire:loading class="flex justify-center items-center w-full h-full">
                        <svg class="animate-spin h-5 w-5 mr-3 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Chargement...
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/docu-sign-send-modal.blade.php ENDPATH**/ ?>