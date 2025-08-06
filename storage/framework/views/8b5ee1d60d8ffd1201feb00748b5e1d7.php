<div>
    

    <div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white">Mes Informations Personnelles</h2>

            <!--[if BLOCK]><![endif]--><?php if($admin): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                            <p class="form-display-text"><?php echo e($matricule); ?></p>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                            <p class="form-display-text"><?php echo e($nom); ?></p>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                            <p class="form-display-text"><?php echo e($prenom ?: 'Non spécifié'); ?></p>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                            <p class="form-display-text"><?php echo e($telephone ?: 'Non spécifié'); ?></p>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pays</label>
                            <p class="form-display-text"><?php echo e($pays ?: 'Non spécifié'); ?></p>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ville</label>
                            <p class="form-display-text"><?php echo e($ville ?: 'Non spécifiée'); ?></p>
                        </div>

                        
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de Passe</label>
                            <p class="form-display-text"><?php echo e($admin->password ? 'Défini' : 'Non défini'); ?></p>
                        </div>

                        
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernier changement mot de passe</label>
                            <p class="form-display-text"><?php echo e($passwordChangedAt); ?></p>
                        </div>

                        
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Passcode</label>
                            <p class="form-display-text"><?php echo e($admin->passcode ? 'Défini' : 'Non défini'); ?></p>
                        </div>

                        
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dernière réinitialisation Passcode</label>
                            <p class="form-display-text"><?php echo e($passcodeResetDate); ?></p>
                        </div>
                    </div>

                    
                    <div class="md:col-span-1 space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de Profil</label>
                            <div class="flex items-center justify-center">
                                <!--[if BLOCK]><![endif]--><?php if($photoProfilUrl): ?>
                                    <img src="<?php echo e($photoProfilUrl); ?>" class="h-32 w-32 object-cover rounded-full shadow-md" alt="Photo de Profil">
                                <?php else: ?>
                                    <div class="h-32 w-32 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-300 text-5xl font-bold">
                                        <?php echo e(strtoupper(substr($prenom, 0, 1))); ?><?php echo e(strtoupper(substr($nom, 0, 1))); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div x-data="{ isFlipping: false }" class="relative perspective-1000">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pièce d'Identité</label>
                            <div class="flip-card w-full h-48 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                <div class="flip-card-inner w-full h-full relative"
                                     
                                     :style="$wire.showPieceIdentiteRecto ? 'transform: rotateY(0deg)' : 'transform: rotateY(180deg)'"
                                     
                                     :class="isFlipping ? 'transition-transform duration-600 ease-in-out' : ''">

                                    
                                    <div class="flip-card-front w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                        <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteRectoUrl): ?>
                                            <img src="<?php echo e($pieceIdentiteRectoUrl); ?>" alt="Pièce d'Identité Recto"
                                                 class="object-contain max-h-full max-w-full cursor-pointer"
                                                 @click="
                                                isFlipping = true; // Active la transition CSS
                                                $wire.togglePieceIdentite(); // Change la propriété Livewire, ce qui inverse le transform: rotateY sur le parent
                                                setTimeout(() => {
                                                    isFlipping = false; // Désactive la transition après l'animation
                                                }, 600); // Doit correspondre à la durée de la transition
                                            ">
                                            <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Recto (cliquez pour Verso)</span>
                                        <?php else: ?>
                                            <p class="text-gray-500 dark:text-gray-400">Aucun Recto disponible</p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>

                                    <div class="flip-card-back w-full h-full absolute bg-gray-100 dark:bg-gray-700 flex items-center justify-center backface-hidden">
                                        <!--[if BLOCK]><![endif]--><?php if($pieceIdentiteVersoUrl): ?>
                                            <img src="<?php echo e($pieceIdentiteVersoUrl); ?>" alt="Pièce d'Identité Verso"
                                                 class="object-contain max-h-full max-w-full cursor-pointer"
                                                 @click="
                                                isFlipping = true; // Active la transition CSS
                                                $wire.togglePieceIdentite(); // Change la propriété Livewire, ce qui inverse le transform: rotateY sur le parent
                                                setTimeout(() => {
                                                    isFlipping = false; // Désactive la transition après l'animation
                                                }, 600); // Doit correspondre à la durée de la transition
                                            ">
                                            <span class="absolute bottom-2 text-xs text-gray-600 dark:text-gray-300">Verso (cliquez pour Recto)</span>
                                        <?php else: ?>
                                            <p class="text-gray-500 dark:text-gray-400">Aucun Verso disponible</p>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code QR de mon Matricule</label>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow-inner flex items-center justify-center">
                                <!--[if BLOCK]><![endif]--><?php if($matricule): ?>

                                    <?php echo QrCode::size(150)->generate($matricule); ?>

                                <?php else: ?>
                                    <p class="text-gray-500 dark:text-gray-400">Matricule non disponible pour QR Code.</p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Matricule: <?php echo e($matricule); ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center text-gray-500 dark:text-gray-400">
                    Impossible de charger votre profil. Veuillez vous reconnecter.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <style>
        .form-display-text {
            @apply px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-sm
            text-gray-800 dark:text-gray-200 font-medium break-words;
        }

        .perspective-1000 {
            perspective: 1000px;
        }
        .flip-card-inner {
            transform-style: preserve-3d;
        }
        .transition-transform {
            transition-property: transform;
        }
        .duration-600 {
            transition-duration: 600ms;
        }
        .ease-in-out {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        .backface-hidden {
            backface-visibility: hidden;
        }
        .flip-card-front {
            transform: rotateY(0deg);
            z-index: 2;
        }
        .flip-card-back {
            transform: rotateY(180deg);
            z-index: 1;
        }
    </style>

</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/profile-display.blade.php ENDPATH**/ ?>