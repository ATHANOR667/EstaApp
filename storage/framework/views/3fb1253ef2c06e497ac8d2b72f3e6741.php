<div> <?php use Carbon\Carbon ;?>

    <div class="flex-grow p-4">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Prestations du <?php echo e($currentDate->locale('fr')->isoFormat('dddd D MMMM YYYY')); ?>

        </h3>

        
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $prestations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prestation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div wire:click="openPrestationDetails(<?php echo e($prestation->id); ?>)"
                 class="bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm p-4 mb-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <!--[if BLOCK]><![endif]--><?php if($prestation->artiste && $prestation->artiste->photo): ?>
                        
                        <img src="<?php echo e($prestation->artiste->photo); ?>" alt="<?php echo e($prestation->artiste->nom); ?>"
                             class="w-12 h-12 rounded-full object-cover border-2"
                             style="border-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>;">
                    <?php else: ?>
                        
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold
                                bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-200 border-2"
                             style="background-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>; color: <?php echo e($prestation->artiste->couleur ? LightenDarkenColor($prestation->artiste->couleur, -80) : '#1f2937'); ?>; border-color: <?php echo e($prestation->artiste->couleur ?? '#cbd5e0'); ?>;">
                            <?php echo e(substr($prestation->nom_artiste_groupe, 0, 1)); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="flex-grow">
                    
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                       <?php if($prestation->artiste && $prestation->artiste->couleur): ?>
                           style="color: <?php echo e($prestation->artiste->couleur); ?>;"
                        <?php endif; ?>>
                        <?php echo e($prestation->nom_artiste_groupe); ?>

                    </p>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <?php echo e(Carbon::parse($prestation->heure_debut_prestation)->format('H:i')); ?> - <?php echo e(Carbon::parse($prestation->heure_fin_prevue)->format('H:i')); ?> | <?php echo e($prestation->lieu_prestation); ?>

                    </p>
                    
                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                        Type: <?php echo e($prestation->type_evenement); ?>

                    </p>
                    
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        Statut: <span class="font-medium
                        <?php echo e($prestation->status == 'redigee' ? 'text-blue-600' : ''); ?>

                        <?php echo e($prestation->status == 'en cours de redaction' ? 'text-purple-600' : ''); ?>

                        <?php echo e($prestation->status == 'annulee' ? 'text-red-600' : ''); ?>

                        <?php echo e($prestation->status == 'terminee' ? 'text-green-600' : ''); ?>">
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

    
    
    <script>
        function LightenDarkenColor(col, amt) {
            var usePound = false;
            if (col[0] == "#") {
                col = col.slice(1);
                usePound = true;
            }
            var num = parseInt(col, 16);
            var r = (num >> 16) + amt;
            if (r > 255) r = 255;
            else if (r < 0) r = 0;
            var b = ((num >> 8) & 0x00FF) + amt;
            if (b > 255) b = 255;
            else if (b < 0) b = 0;
            var g = (num & 0x0000FF) + amt;
            if (g > 255) g = 255;
            else if (g < 0) g = 0;
            return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
        }
    </script>


</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/day-view.blade.php ENDPATH**/ ?>