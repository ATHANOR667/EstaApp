<?php $__env->startSection('title','CALENDAR'); ?>

<?php $__env->startSection('content'); ?>

    <div class="py-8">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.calendar.calendar');

$__html = app('livewire')->mount($__name, $__params, 'lw-86247629-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.connected-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/admin/pages/calendar.blade.php ENDPATH**/ ?>