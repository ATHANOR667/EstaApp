<div>
    

    <div x-data="{ show: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?> }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-center min-h-screen">
            
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"></div>

            
            <div x-show="show" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all w-full max-w-5xl max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                        <?php echo e($admin ? 'Profil de ' . $admin->prenom . ' ' . $admin->nom : 'Chargement...'); ?>

                    </h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($admin): ?>
                    <form wire:submit.prevent="updateAdmin" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            
                            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                                    <input type="text" id="nom" wire:model.defer="nom"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                                    <input type="text" id="prenom" wire:model.defer="prenom"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (optionnel)</label>
                                    <input type="email" id="email" wire:model.defer="email"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                                    <input type="text" id="telephone" wire:model.defer="telephone"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="pays" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays (optionnel)</label>
                                    <input type="text" id="pays" wire:model.defer="pays"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['pays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div>
                                    <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville (optionnel)</label>
                                    <input type="text" id="ville" wire:model.defer="ville"
                                           class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['ville'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ville'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                
                                <div class="sm:col-span-2">
                                    <label for="password_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mot de Passe</label>
                                    <div class="relative">
                                        <input type="text" id="password_display"
                                               value="<?php echo e($admin->password ? '********' : 'Non défini'); ?>"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                
                                <div class="sm:col-span-2">
                                    <label for="passcode_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passcode</label>
                                    <div class="relative">
                                        <input type="text" id="passcode_display"
                                               value="<?php echo e($admin->passcode ? '********' : 'Non défini'); ?>"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                
                                <div class="sm:col-span-2">
                                    <label for="password_changed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dernier changement mot de passe</label>
                                    <div class="relative">
                                        <input type="text" id="password_changed_at"
                                               value="<?php echo e($admin->password_changed_at ? $admin->password_changed_at->format('d/m/Y H:i') : 'Jamais'); ?>"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>

                                
                                <div class="sm:col-span-2">
                                    <label for="passcode_reset_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut réinitialisation Passcode</label>
                                    <div class="relative">
                                        <input type="text" id="passcode_reset_date"
                                               value="<?php echo e($admin->passcode_reset_date ? $admin->passcode_reset_date->format('d/m/Y H:i') : 'Jamais'); ?>"
                                               class="form-input w-full px-4 py-2 text-base rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 cursor-not-allowed"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="md:col-span-1 space-y-6">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo de Profil</label>
                                    <div class="flex items-center space-x-3">
                                        <!--[if BLOCK]><![endif]--><?php if($newPhotoProfil): ?>
                                            <img src="<?php echo e($newPhotoProfil->temporaryUrl()); ?>" class="h-24 w-24 object-cover rounded-full shadow-md" alt="Nouvelle Photo">
                                        <?php elseif($admin->photoProfil): ?>
                                            <img src="<?php echo e(asset('storage/' . $admin->photoProfil)); ?>" class="h-24 w-24 object-cover rounded-full shadow-md" alt="Photo Actuelle">
                                        <?php else: ?>
                                            <div class="h-24 w-24 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-3xl font-bold">
                                                <?php echo e(strtoupper(substr($prenom, 0, 1))); ?><?php echo e(strtoupper(substr($nom, 0, 1))); ?>

                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <div>
                                            <input type="file" id="newPhotoProfil" wire:model="newPhotoProfil"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newPhotoProfil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($admin->photoProfil): ?>
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePhotoProfil" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer la photo actuelle</span>
                                                </label>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>

                                
                                <div x-data="{ showPieceIdentiteRecto: <?php if ((object) ('showPieceIdentiteRecto') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showPieceIdentiteRecto'->value()); ?>')<?php echo e('showPieceIdentiteRecto'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showPieceIdentiteRecto'); ?>')<?php endif; ?>, isFlipping: false }" class="relative perspective-1000">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièce d'Identité</label>
                                    <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                        <div class="flip-card-inner w-full h-full relative" :class="isFlipping ? 'flipping' : ''">
                                            
                                            <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden"
                                                 x-show="showPieceIdentiteRecto"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:leave="ease-in duration-300"
                                                 x-transition:leave-end="opacity-0">
                                                <!--[if BLOCK]><![endif]--><?php if($admin->pieceIdentiteRecto): ?>
                                                    <img src="<?php echo e(asset('storage/' . $admin->pieceIdentiteRecto)); ?>" alt="Pièce d'Identité Recto"
                                                         class="object-contain max-h-full max-w-full cursor-pointer"
                                                         @click="isFlipping = true; setTimeout(() => { showPieceIdentiteRecto = !showPieceIdentiteRecto; isFlipping = false; }, 300);">
                                                    <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto (cliquez pour Verso)</span>
                                                <?php else: ?>
                                                    <p class="text-gray-500 dark:text-gray-400">Aucun Recto</p>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            
                                            <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden"
                                                 x-show="!showPieceIdentiteRecto"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:leave="ease-in duration-300"
                                                 x-transition:leave-end="opacity-0">
                                                <!--[if BLOCK]><![endif]--><?php if($admin->pieceIdentiteVerso): ?>
                                                    <img src="<?php echo e(asset('storage/' . $admin->pieceIdentiteVerso)); ?>" alt="Pièce d'Identité Verso"
                                                         class="object-contain max-h-full max-w-full cursor-pointer"
                                                         @click="isFlipping = true; setTimeout(() => { showPieceIdentiteRecto = !showPieceIdentiteRecto; isFlipping = false; }, 300);">
                                                    <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso (cliquez pour Recto)</span>
                                                <?php else: ?>
                                                    <p class="text-gray-500 dark:text-gray-400">Aucun Verso</p>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <div>
                                            <label for="newPieceIdentiteRecto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nouveau Recto (optionnel)</label>
                                            <input type="file" id="newPieceIdentiteRecto" wire:model="newPieceIdentiteRecto"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newPieceIdentiteRecto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($admin->pieceIdentiteRecto && !$newPieceIdentiteRecto): ?>
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePieceIdentiteRecto" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer le Recto actuel</span>
                                                </label>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div>
                                            <label for="newPieceIdentiteVerso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nouveau Verso (optionnel)</label>
                                            <input type="file" id="newPieceIdentiteVerso" wire:model="newPieceIdentiteVerso"
                                                   class="block w-full text-base text-gray-500 file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                                  dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-blue-300 dark:hover:file:bg-gray-600">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newPieceIdentiteVerso'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($admin->pieceIdentiteVerso && !$newPieceIdentiteVerso): ?>
                                                <label class="inline-flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    <input type="checkbox" wire:model.defer="deletePieceIdentiteVerso" class="form-checkbox rounded text-red-600">
                                                    <span class="ml-2">Supprimer le Verso actuel</span>
                                                </label>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code QR du Matricule</label>
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-inner flex items-center justify-center">
                                        <?php echo QrCode::size(150)->generate($admin->matricule); ?>

                                    </div>
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Matricule: <?php echo e($admin->matricule); ?></p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-6 border-t pt-6 border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attribuer les Rôles</label>
                            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->availableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <label for="role-<?php echo e($role->id); ?>" class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" id="role-<?php echo e($role->id); ?>" value="<?php echo e($role->id); ?>" wire:model.defer="selectedRoles"
                                               class="form-checkbox h-5 w-5 text-blue-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded
                                              focus:ring-blue-500 dark:focus:ring-blue-500">
                                        <span class="ml-2 text-base"><?php echo e($role->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-gray-500 dark:text-gray-400 col-span-full">Aucun rôle disponible pour le guard 'admin'.</p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedRoles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedRoles.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" wire:click="closeModal"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                Annuler
                            </button>
                            
                            <button type="button" wire:click="generatePdf"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                Générer PDF
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                Mettre à jour le Profil
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        Chargement du profil ou administrateur non trouvé...
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/super-admin/manage-admins/admin-profile-card.blade.php ENDPATH**/ ?>