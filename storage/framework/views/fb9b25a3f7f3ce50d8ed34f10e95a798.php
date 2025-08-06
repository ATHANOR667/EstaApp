<div class="flex-grow">
    
    <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
        <div>Lun</div>
        <div>Mar</div>
        <div>Mer</div>
        <div>Jeu</div>
        <div>Ven</div>
        <div>Sam</div>
        <div>Dim</div>
    </div>

    
    <div class="grid grid-cols-7 gap-1 h-full">
        <!-- Jours vides avant le début du mois (pour aligner le 1er jour) -->
        <!--[if BLOCK]><![endif]--><?php for($i = 0; $i < $blankDaysBefore; $i++): ?>
            <div class="h-28 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500"></div>
        <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Jours du mois -->
        <!--[if BLOCK]><![endif]--><?php for($day = 1; $day <= $daysInMonth; $day++): ?>
            <?php
                $currentDay = $firstDayOfMonth->copy()->day($day); // Crée une instance Carbon pour le jour actuel de la boucle
                $dateString = $currentDay->format('Y-m-d'); // Format de date pour la communication Livewire
                $isToday = $currentDay->isToday(); // Vérifie si c'est le jour actuel
                $dayPrestations = $this->getPrestationsForDay($dateString); // Récupère les prestations pour ce jour
            ?>
            <div wire:click="openDayDetails('<?php echo e($dateString); ?>')" 
            class="h-28 rounded-lg p-2 flex flex-col cursor-pointer transition-all duration-200
                 <?php echo e($isToday ? 'bg-blue-100 dark:bg-blue-700 border-2 border-blue-500 dark:border-blue-400' : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>

                 relative overflow-hidden group">
                
                <span class="text-sm font-semibold <?php echo e($isToday ? 'text-blue-800 dark:text-blue-100' : 'text-gray-800 dark:text-gray-100'); ?>">
                    <?php echo e($day); ?>

                </span>

                
                <!--[if BLOCK]><![endif]--><?php if($dayPrestations->isNotEmpty()): ?>
                    <div class="mt-1 flex flex-wrap gap-1 custom-scrollbar overflow-y-auto max-h-[calc(100%-24px)]">
                        
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $dayPrestations->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prestation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center text-xs px-2 py-1 rounded-full text-white
                                        <?php echo e($prestation->status === 'redigee' ? 'bg-blue-500' : 'bg-gray-500'); ?>"
                                 
                                 style="<?php echo e($prestation->artiste && $prestation->artiste->couleur ? 'background-color:' . $prestation->artiste->couleur . ';' : ''); ?>">
                                <!--[if BLOCK]><![endif]--><?php if($prestation->artiste && $prestation->artiste->photo): ?>
                                    
                                    <img src="<?php echo e($prestation->artiste->photo); ?>" alt="<?php echo e($prestation->artiste->nom); ?>" class="w-4 h-4 rounded-full mr-1 object-cover">
                                <?php else: ?>
                                    
                                    <div class="w-4 h-4 rounded-full mr-1 flex items-center justify-center bg-white text-gray-800 font-bold text-xs">
                                        <?php echo e(substr($prestation->nom_artiste_groupe, 0, 1)); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <span class="truncate"><?php echo e($prestation->nom_artiste_groupe); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <!--[if BLOCK]><![endif]--><?php if($dayPrestations->count() > 3): ?>
                            <span class="text-xs text-gray-600 dark:text-gray-300 ml-1">+<?php echo e($dayPrestations->count() - 3); ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/month-view.blade.php ENDPATH**/ ?>