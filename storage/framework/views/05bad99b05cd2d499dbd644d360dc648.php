<div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg border border-blue-100 dark:border-blue-700">

    <h3 class="text-3xl font-extrabold text-blue-900 dark:text-blue-100 mb-8 flex items-center">
        <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"/>
        </svg>
        Statistiques des Rôles & Permissions
        <span class="ml-4 px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200 text-sm font-semibold rounded-full shadow-inner">
            Guard: <?php echo e($guardName); ?>

        </span>
    </h3>

    
    <div class="mb-8 border-b pb-6 border-blue-200 dark:border-blue-700">
        <h4 class="text-xl font-semibold text-blue-700 dark:text-blue-200 mb-4">Catégories de Permissions</h4>
        <div class="flex flex-wrap gap-4 text-sm">
            
            <!--[if BLOCK]><![endif]--><?php if(!empty($permissionCategories)): ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissionCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-800 rounded-full text-blue-700 dark:text-blue-300 shadow-sm">
                        <span class="font-semibold capitalize"><?php echo e(str_replace('_', ' ', $categoryName)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php else: ?>
                <p class="text-blue-500 dark:text-blue-400">Aucune catégorie de permission définie.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="mb-10 pt-6 border-b pb-6 border-blue-200 dark:border-blue-700">
        <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-100 mb-6 flex items-center">
            <svg class="h-6 w-6 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.7-7 8.94V12H5V6.3l7-3.11v8.79z"/>
            </svg>
            Permissions Attribuées aux Rôles
        </h4>

        <!--[if BLOCK]><![endif]--><?php if(empty($permissionRoleCounts)): ?>
            <div class="bg-blue-50 dark:bg-blue-700 p-6 rounded-lg text-center text-blue-500 dark:text-blue-400">
                <p class="mb-2">Aucune statistique de permission disponible pour le guard '<?php echo e($guardName); ?>'.</p>
                <p>Pour voir des données ici, veuillez créer des permissions et les attacher à des rôles.</p>
            </div>
        <?php else: ?>
            <?php
                $permissionsGroupedByCategory = collect($permissionRoleCounts)->groupBy('categorie');
            ?>

            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissionsGroupedByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $permissionsInGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-8">
                    <h5 class="text-xl font-semibold text-blue-700 dark:text-blue-300 mb-4 border-b border-blue-200 dark:border-blue-600 pb-2">
                        Catégorie: <span class="capitalize"><?php echo e(str_replace('_', ' ', $category ?: 'Non Catégorisée')); ?></span>
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissionsInGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissionId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $percentage = $allRolesCount > 0 ? round(($data['count'] / $allRolesCount) * 100) : 0;
                            ?>
                            <div class="bg-blue-50 dark:bg-blue-700 p-5 rounded-lg shadow-sm border border-blue-300 dark:border-blue-600 hover:shadow-lg transition-shadow duration-200">
                                <h6 class="font-bold text-lg mb-2 text-blue-800 dark:text-blue-100">
                                    <?php echo e($data['name']); ?>

                                </h6>
                                <div class="mb-3">
                                    <p class="text-blue-700 dark:text-blue-200 text-sm">
                                        Attribuée à <span class="font-bold"><?php echo e($data['count']); ?></span> rôle(s)
                                        <!--[if BLOCK]><![endif]--><?php if($allRolesCount > 0): ?>
                                            sur <?php echo e($allRolesCount); ?> rôles
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        :
                                    </p>
                                    <div class="w-full bg-blue-200 rounded-full h-2.5 dark:bg-blue-600 mt-2">
                                        <div class="h-2.5 rounded-full bg-blue-500" style="width: <?php echo e($percentage); ?>%"></div>
                                    </div>
                                    <p class="text-right text-xs text-blue-500 dark:text-blue-400 mt-1"><?php echo e($percentage); ?>%</p>
                                </div>

                                <!--[if BLOCK]><![endif]--><?php if($data['count'] > 0): ?>
                                    <h6 class="font-medium text-blue-700 dark:text-blue-300 mb-1">Rôles associés :</h6>
                                    <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 text-sm space-y-0.5 max-h-20 overflow-y-auto custom-scrollbar pr-2">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $data['roles']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($roleName); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </ul>
                                <?php else: ?>
                                    <p class="text-blue-500 dark:text-blue-400 text-sm mt-1">Aucun rôle ne dispose de cette permission.</p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div class="pt-6">
        <h4 class="text-2xl font-bold text-blue-800 dark:text-blue-100 mb-6 flex items-center">
            <svg class="h-6 w-6 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            Rôles Attribués aux Utilisateurs
        </h4>
        <!--[if BLOCK]><![endif]--><?php if(empty($roleUserCounts)): ?>
            <div class="bg-blue-50 dark:bg-blue-700 p-6 rounded-lg text-center text-blue-500 dark:text-blue-400">
                <p class="mb-2">Aucune statistique de rôle disponible pour le guard '<?php echo e($guardName); ?>'.</p>
                <p>Pour voir des données ici, veuillez créer des rôles et les attribuer à des utilisateurs.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php
                    $totalUsersWithRoles = $userModelClass::all()->count() ;
                ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roleUserCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-blue-50 dark:bg-blue-700 p-5 rounded-lg shadow-sm border border-blue-300 dark:border-blue-600 hover:shadow-lg transition-shadow duration-200">
                        <h5 class="font-bold text-lg mb-2 text-blue-800 dark:text-blue-100"><?php echo e($data['name']); ?></h5>
                        <p class="text-blue-700 dark:text-blue-200 text-sm mb-3">
                            Attribué à <span class="font-bold"><?php echo e($data['count']); ?></span> utilisateur(s) :
                        </p>
                        <?php
                            $percentageUsers = $totalUsersWithRoles > 0 ? round(($data['count'] / $totalUsersWithRoles) * 100) : 0;
                        ?>
                        <div class="w-full bg-blue-200 rounded-full h-2.5 dark:bg-blue-600 mt-2">
                            <div class="h-2.5 rounded-full bg-blue-500" style="width: <?php echo e($percentageUsers); ?>%"></div>
                        </div>
                        <p class="text-right text-xs text-blue-500 dark:text-blue-400 mt-1"><?php echo e($percentageUsers); ?>% des utilisateurs avec rôles</p>

                        <!--[if BLOCK]><![endif]--><?php if($data['count'] > 0): ?>
                            <h6 class="font-medium text-blue-700 dark:text-blue-300 mb-1">Utilisateurs associés :</h6>
                            <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 text-sm space-y-0.5 max-h-20 overflow-y-auto custom-scrollbar pr-2">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $data['users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($userName); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </ul>
                        <?php else: ?>
                            <p class="text-blue-500 dark:text-blue-400 text-sm mt-1">Aucun utilisateur ne dispose de ce rôle.</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #e0f2f7;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #90caf9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64b5f6;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #90caf9 #e0f2f7;
        }
    </style>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/gestion-roles/permission-stats.blade.php ENDPATH**/ ?>