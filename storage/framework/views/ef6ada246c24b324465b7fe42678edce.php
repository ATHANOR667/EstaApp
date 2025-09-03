<?php $__env->startSection('title', 'MANAGE TEAMS'); ?>

<?php $__env->startSection('content'); ?>
    <div
        x-data="{
            showSpinner: false,
            init() {
                Livewire.on('loading', () => {
                    this.showSpinner = true;
                });
                Livewire.on('success', () => {
                    this.showSpinner = false;
                });
                Livewire.on('error', () => {
                    this.showSpinner = false;
                });
            }
        }"
        class="min-h-screen flex flex-col gap-6 p-4 sm:p-6"
    >
        <div x-show="showSpinner"
             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-50">
            <div class="animate-spin rounded-full h-24 w-24 sm:h-32 sm:w-32 border-t-4 border-b-4 border-blue-500"></div>
        </div>

        <!-- Composant de la liste des artistes et du formulaire de création/édition -->
        <div class="w-full">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('super-admin.manage-teams.artistes-list');

$__html = app('livewire')->mount($__name, $__params, 'lw-207071913-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <!-- Composant de gestion de l'équipe des admins -->
        <div class="w-full">
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('super-admin.manage-teams.team-manager');

$__html = app('livewire')->mount($__name, $__params, 'lw-207071913-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>

        <!-- Modale de création/édition d'artiste -->
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('super-admin.manage-teams.artiste-form');

$__html = app('livewire')->mount($__name, $__params, 'lw-207071913-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        <!-- Composant de la carte de profil de l'administrateur -->
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('super-admin.manage-admins.admin-profile-card');

$__html = app('livewire')->mount($__name, $__params, 'lw-207071913-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/super-admin/pages/manage-teams.blade.php ENDPATH**/ ?>