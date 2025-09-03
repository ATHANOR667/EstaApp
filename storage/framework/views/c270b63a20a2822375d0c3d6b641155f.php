<div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-md transition-colors duration-300">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 sm:mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Liste des Artistes</h2>
        <button wire:click="openCreateModal" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
            Créer un Artiste
        </button>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un artiste..."
               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
    </div>

    <!--[if BLOCK]><![endif]--><?php if($artistes->count()): ?>
        <!-- Mobile: Card layout -->
        <div class="space-y-4 sm:hidden">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $artistes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artiste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div wire:key="artiste-<?php echo e($artiste->id); ?>" class="p-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 <?php if($selectedArtiste && $selectedArtiste->id === $artiste->id): ?> bg-blue-50 dark:bg-blue-900 <?php endif; ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3" wire:click="selectArtiste(<?php echo e($artiste->id); ?>)">
                            <!--[if BLOCK]><![endif]--><?php if($artiste->photo): ?>
                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e(asset('storage/' . $artiste->photo)); ?>" alt="<?php echo e($artiste->nom); ?>">
                            <?php else: ?>
                                <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold">
                                    <?php echo e(strtoupper(substr($artiste->nom, 0, 1))); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e($artiste->nom); ?></span>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click.stop="openEditModal(<?php echo e($artiste->id); ?>)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200" aria-label="Éditer l'artiste">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button wire:click.stop="deleteArtiste(<?php echo e($artiste->id); ?>)" wire:confirm="Êtes-vous sûr de vouloir supprimer cet artiste ?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200" aria-label="Supprimer l'artiste">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- Desktop: Table layout -->
        <div class="hidden sm:block overflow-x-auto rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $artistes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artiste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php if($selectedArtiste && $selectedArtiste->id === $artiste->id): ?> bg-blue-50 dark:bg-blue-900 <?php else: ?> hover:bg-gray-50 dark:hover:bg-gray-700 <?php endif; ?> cursor-pointer">
                        <td class="px-4 py-4 whitespace-nowrap" wire:click="selectArtiste(<?php echo e($artiste->id); ?>)">
                            <div class="flex items-center">
                                <!--[if BLOCK]><![endif]--><?php if($artiste->photo): ?>
                                    <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e(asset('storage/' . $artiste->photo)); ?>" alt="<?php echo e($artiste->nom); ?>">
                                <?php else: ?>
                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-300 font-bold">
                                        <?php echo e(strtoupper(substr($artiste->nom, 0, 1))); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100"><?php echo e($artiste->nom); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click.stop="openEditModal(<?php echo e($artiste->id); ?>)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200" aria-label="Éditer l'artiste">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button wire:click.stop="deleteArtiste(<?php echo e($artiste->id); ?>)" wire:confirm="Êtes-vous sûr de vouloir supprimer cet artiste ?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2" aria-label="Supprimer l'artiste">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($artistes->links()); ?>

        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 dark:text-gray-400">Aucun artiste trouvé.</p>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/super-admin/manage-teams/artistes-list.blade.php ENDPATH**/ ?>