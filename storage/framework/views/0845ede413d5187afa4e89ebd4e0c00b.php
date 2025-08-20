<div>
    <?php
        use Carbon\Carbon;
        function isColorDark($hexColor) {
            $hexColor = ltrim($hexColor, '#');
            if (strlen($hexColor) == 3) {
                $r = hexdec(str_repeat(substr($hexColor, 0, 1), 2));
                $g = hexdec(str_repeat(substr($hexColor, 1, 1), 2));
                $b = hexdec(str_repeat(substr($hexColor, 2, 1), 2));
            } else {
                $r = hexdec(substr($hexColor, 0, 2));
                $g = hexdec(substr($hexColor, 2, 2));
                $b = hexdec(substr($hexColor, 4, 2));
            }
            $luma = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luma < 0.5;
        }
    ?>

    <div class="flex-grow p-4">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Prestations du <?php echo e($currentDate->locale('fr')->isoFormat('dddd D MMMM YYYY')); ?>

        </h3>

        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $prestations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prestation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div wire:click="openPrestationDetails(<?php echo e($prestation->id); ?>)"
                 class="bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200
                        flex flex-col sm:flex-row items-center sm:items-start space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex-shrink-0">
                    <!--[if BLOCK]><![endif]--><?php if($prestation->artiste && $prestation->artiste->photo): ?>
                        <img src="<?php echo e($prestation->artiste->photo); ?>" alt="<?php echo e($prestation->artiste->nom); ?>"
                             class="w-12 h-12 rounded-full object-cover border-2"
                             style="border-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>;">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold border-2
                                <?php echo e(isColorDark($prestation->artiste->couleur ?? '#cbd5e0') ? 'text-white' : 'text-black'); ?>"
                             style="background-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>; border-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>;">
                            <?php echo e(substr($prestation->artiste->nom, 0, 1)); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="flex-grow text-center sm:text-left">
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                       <?php if($prestation->artiste && $prestation->artiste->couleur): ?>
                           style="color: <?php echo e($prestation->artiste->couleur); ?>;"
                        <?php endif; ?>>
                        <?php echo e($prestation->artiste->nom); ?>

                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <?php echo e(Carbon::parse($prestation->heure_debut_prestation)->format('H:i')); ?> - <?php echo e(Carbon::parse($prestation->heure_fin_prevue)->format('H:i')); ?> | <?php echo e($prestation->lieu_prestation); ?>

                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                        Type: <?php echo e($prestation->type_evenement); ?>

                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        Statut:
                        <span class="font-medium
                            <?php if($prestation->status == 'redigee'): ?> text-blue-600
                            <?php elseif($prestation->status == 'en cours de redaction'): ?> text-purple-600
                            <?php elseif($prestation->status == 'annulee'): ?> text-red-600
                            <?php elseif($prestation->status == 'terminee'): ?> text-green-600
                            <?php endif; ?>">
                            <?php echo e(ucfirst($prestation->status)); ?>

                        </span>
                    </p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-600 dark:text-gray-300 text-center py-8">
                Aucune prestation prévue pour cette journée.
            </p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div wire:loading wire:target="openPrestationDetails" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center rounded-lg z-10">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Chargement...</span>
        </div>
    </div>

</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/view-mode/day-view.blade.php ENDPATH**/ ?>