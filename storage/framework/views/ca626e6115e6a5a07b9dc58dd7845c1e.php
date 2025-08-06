<?php $__env->startSection('title', 'PROFILE'); ?>

<?php $__env->startSection('content'); ?>

    <div x-data="{ openEmailReset: false, openPasswordReset: false }">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white">Mon Profil</h1>

        
        <div class="mb-8">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.profile-display', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1066740527-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <?php if(!is_null($admin->email)): ?>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 transition-colors duration-300 ease-in-out">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Changer l'Email Associé</h2>
                <button type="button" @click="openEmailReset = !openEmailReset" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <span x-text="openEmailReset ? 'Fermer' : 'Changer l\'Email'"></span>
                </button>
                <div x-show="openEmailReset" x-transition class="mt-6">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('auth.email-reset-form', ['guard' => 'admin']);

$__html = app('livewire')->mount($__name, $__params, 'lw-1066740527-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-colors duration-300 ease-in-out">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Changer le Mot de Passe</h2>
                <button type="button" @click="openPasswordReset = !openPasswordReset" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <span x-text="openPasswordReset ? 'Fermer' : 'Changer le Mot de Passe'"></span>
                </button>
                <div x-show="openPasswordReset" x-transition class="mt-6">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('auth.password-reset-form', ['guard' => 'admin']);

$__html = app('livewire')->mount($__name, $__params, 'lw-1066740527-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/admin/pages/profile.blade.php ENDPATH**/ ?>