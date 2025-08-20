<div>
    <!--[if BLOCK]><![endif]--><?php if($showContratListModal): ?>
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50"
             x-data="{ show: $wire.entangle('showContratListModal') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.away="show = false; $wire.call('closeModal')">

            
            <div wire:loading class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center rounded-lg z-20">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Chargement...</span>
                </div>
            </div>

            
            <div wire:loading.class="opacity-50 pointer-events-none" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-3xl mx-auto relative transform transition-all duration-300 overflow-y-auto max-h-[90vh]">
                <button wire:click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Contrats pour : <?php echo e($prestation->artiste->nom ?? 'Artiste non défini'); ?> - <?php echo e($prestation->date_prestation ? \Carbon\Carbon::parse($prestation->date_prestation)->locale('fr')->isoFormat('D MMMM YYYY') : 'N/A'); ?>

                </h3>

                <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
                    <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-4">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php if(session()->has('error')): ?>
                    <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-4">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <div class="mb-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                    <button wire:click="openContratForm(<?php echo e($prestationId); ?>)" class="w-full sm:w-auto px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Écrire un contrat
                    </button>
                    <button wire:click="openContratFormWithAi(<?php echo e($prestationId); ?>)" class="w-full sm:w-auto px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
                        Générer avec IA
                    </button>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($contrats->isEmpty()): ?>
                    <p class="text-gray-600 dark:text-gray-300 text-center py-8">Aucun contrat pour cette prestation.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                <div>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100"><?php echo e($contrat->prestation->artiste->nom); ?></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Créé le : <?php echo e(\Carbon\Carbon::parse($contrat->created_at)->locale('fr')->isoFormat('D MMMM YYYY, HH:mm')); ?></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Statut : <span class="font-medium <?php echo e($contrat->status == 'signed' ? 'text-green-600' : ($contrat->status == 'rejected' ? 'text-red-600' : 'text-yellow-600')); ?>"><?php echo e(\App\Models\Contrat::STATUSES[$contrat->status] ?? ucfirst($contrat->status)); ?></span></p>
                                </div>

                                
                                <div class="mt-2 sm:mt-0 flex flex-wrap gap-2 justify-start sm:justify-end">
                                    
                                    <div class="flex gap-2">
                                        <button wire:click="viewContrat(<?php echo e($contrat->id); ?>)" class="px-3 py-1 rounded-md bg-gray-500 text-white hover:bg-gray-600">Voir</button>
                                        <button wire:click="editContrat(<?php echo e($contrat->id); ?>)" class="px-3 py-1 rounded-md bg-blue-500 text-white hover:bg-blue-600">Éditer</button>
                                    </div>
                                    <div class="flex gap-2">
                                        <!--[if BLOCK]><![endif]--><?php if($contrat->status == 'draft'): ?>
                                            <button wire:click="sendContract(<?php echo e($contrat->id); ?>)" class="px-3 py-1 rounded-md bg-green-500 text-white hover:bg-green-600">Envoyer</button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <button wire:click="downloadPdf(<?php echo e($contrat->id); ?>)" class="px-3 py-1 rounded-md bg-indigo-500 text-white hover:bg-indigo-600">PDF</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="mt-6 flex justify-end">
                    <button wire:click="closeModal" class="px-5 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/contrat/contrat-list-modal.blade.php ENDPATH**/ ?>