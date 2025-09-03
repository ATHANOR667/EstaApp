<div x-data="{ open: true }" x-init="$nextTick(() => setTimeout(() => open = false, 5000))" x-show="open" x-cloak
     class="fixed bottom-4 right-4 z-50 w-[90%] sm:w-96 max-w-full">
    <?php if(session('success')): ?>
        <div class="relative bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full">
            <span><?php echo e(session('success')); ?></span>
            <button @click="open = false" class="ml-4 text-white hover:text-gray-200 focus:outline-none" aria-label="Fermer la notification">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    <?php elseif(session('error')): ?>
        <div class="relative bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full">
            <span><?php echo e(session('error')); ?></span>
            <button @click="open = false" class="ml-4 text-white hover:text-gray-200 focus:outline-none" aria-label="Fermer la notification">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/toast.blade.php ENDPATH**/ ?>