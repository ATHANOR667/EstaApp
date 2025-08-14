<div>

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl relative min-h-[600px] flex flex-col">
        <!-- En-tête du calendrier et sélecteur de vue -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <!-- Navigation (Année et Mois/Jour) -->
            <div class="flex items-center space-x-2">
                <!--[if BLOCK]><![endif]--><?php if($viewMode === 'month'): ?>
                    
                    <button wire:click="goToPreviousYear" class="p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                
                <button wire:click="goToPrevious" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 text-center capitalize">
                    <?php echo e($this->currentPeriodTitle); ?>

                </h2>
                
                <button wire:click="goToNext" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <!--[if BLOCK]><![endif]--><?php if($viewMode === 'month'): ?>
                    
                    <button wire:click="goToNextYear" class="p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7" /></svg>
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Sélecteur de vue et bouton Ajouter Prestation -->
            <div class="flex flex-wrap items-center gap-2">
                
                <div class="flex space-x-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-full shadow-inner">
                    <button wire:click="setViewMode('month')"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                        <?php echo e($viewMode === 'month' ? 'bg-blue-600 text-white shadow' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>">
                        Mois
                    </button>
                    <button wire:click="setViewMode('day')"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                        <?php echo e($viewMode === 'day' ? 'bg-blue-600 text-white shadow' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>">
                        Jour
                    </button>
                </div>
                
                <button wire:click="openPrestationFormModal"
                        class="ml-4 px-4 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    + Ajouter Prestation
                </button>
            </div>
        </div>

        <!-- Contenu du calendrier basé sur le mode de vue (appel des composants enfants) -->
        <!--[if BLOCK]><![endif]--><?php if($viewMode === 'month'): ?>
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.view-mode.month-view', [
            'currentDate' => $currentDate,
            'prestations' => $prestations,
            ]);

$__html = app('livewire')->mount($__name, $__params, 'month-view-' . $currentDate->format('Y-m-d') . '-' . $viewMode, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php elseif($viewMode === 'day'): ?>
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.view-mode.day-view', [
            'currentDate' => $currentDate,
            'prestations' => $prestations,
            ]);

$__html = app('livewire')->mount($__name, $__params, 'day-view-' . $currentDate->format('Y-m-d') . '-' . $viewMode, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Modales (appel des composants enfants) -->


        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.prestation.prestation-details-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-2931593000-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.prestation.prestation-form-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-2931593000-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.contrat.contrat-list-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-2931593000-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.contrat.contrat-form-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-2931593000-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.contrat.docu-sign-send-modal');

$__html = app('livewire')->mount($__name, $__params, 'lw-2931593000-4', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    <style>
        /* Styles personnalisés pour la scrollbar (pour les événements dans les cellules) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0; /* gray-300 */
            border-radius: 2px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563; /* gray-600 */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0; /* gray-400 */
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280; /* gray-500 */
        }
    </style>


</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/calendar.blade.php ENDPATH**/ ?>