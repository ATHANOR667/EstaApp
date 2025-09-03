
<div>
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50"
             x-data="{ show: <?php if ((object) ('showModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'->value()); ?>')<?php echo e('showModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showModal'); ?>')<?php endif; ?> }" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @click.away="show = false; $wire.closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-2xl mx-auto relative transform transition-all duration-300 overflow-y-auto max-h-[90vh]">
                <button wire:click="closeModal"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    <?php echo e($editingPrestationId ? 'Modifier la prestation' : 'Créer une nouvelle prestation'); ?>

                </h3>

                <!--[if BLOCK]><![endif]--><?php if($overlappingWarning): ?>
                    <div class="bg-yellow-100 dark:bg-yellow-800 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-100 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo e($overlappingWarning); ?></span>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <form wire:submit.prevent="savePrestation" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="artiste_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sélectionner un artiste <span class="text-red-500">*</span></label>
                            <select id="artiste_id" wire:model.live="form.artiste_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">-- Sélectionner un artiste --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $artistes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artiste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($artiste->id); ?>"><?php echo e($artiste->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.artiste_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="display_nom_artiste_groupe" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom de l'artiste ou du groupe</label>
                            <input type="text" id="display_nom_artiste_groupe"
                                   value="<?php echo e($form['artiste_id'] ? ($artistes->firstWhere('id', $form['artiste_id'])->nom ?? '') : ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 cursor-not-allowed"
                                   readonly>
                        </div>
                        <div>
                            <label for="nom_structure_contractante" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom de la structure contractante / organisateur <span class="text-red-500">*</span></label>
                            <input type="text" id="nom_structure_contractante" wire:model="form.nom_structure_contractante"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.nom_structure_contractante'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="nom_representant_legal_artiste" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du représentant légal de l'artiste</label>
                            <input type="text" id="nom_representant_legal_artiste" wire:model="form.nom_representant_legal_artiste"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div>
                            <label for="contact_artiste" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact de l'artiste (téléphone, email)</label>
                            <input type="text" id="contact_artiste" wire:model="form.contact_artiste"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div>
                            <label for="contact_organisateur" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact de l'organisateur</label>
                            <input type="text" id="contact_organisateur" wire:model="form.contact_organisateur"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="date_prestation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de la prestation <span class="text-red-500">*</span></label>
                            <input type="date" id="date_prestation" wire:model="form.date_prestation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.date_prestation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="heure_debut_prestation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heure de début <span class="text-red-500">*</span></label>
                            <input type="time" id="heure_debut_prestation" wire:model="form.heure_debut_prestation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.heure_debut_prestation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="heure_fin_prevue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heure de fin prévue <span class="text-red-500">*</span></label>
                            <input type="time" id="heure_fin_prevue" wire:model="form.heure_fin_prevue"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.heure_fin_prevue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="lieu_prestation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lieu de la prestation <span class="text-red-500">*</span></label>
                            <input type="text" id="lieu_prestation" wire:model="form.lieu_prestation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.lieu_prestation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="duree_effective_performance" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durée effective de la performance (min)</label>
                            <input type="number" id="duree_effective_performance" wire:model="form.duree_effective_performance"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div>
                            <label for="type_evenement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'événement <span class="text-red-500">*</span></label>
                            <select id="type_evenement" wire:model="form.type_evenement"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">Sélectionner un type</option>
                                <option value="Concert">Concert</option>
                                <option value="Mariage">Mariage</option>
                                <option value="Soirée privée">Soirée privée</option>
                                <option value="Festival">Festival</option>
                                <option value="Autre">Autre</option>
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.type_evenement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="nombre_sets_morceaux" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de sets / morceaux prévus</label>
                            <input type="number" id="nombre_sets_morceaux" wire:model="form.nombre_sets_morceaux"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Conditions financières</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="montant_total_cachet" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Montant total du cachet</label>
                            <input type="number" step="0.01" id="montant_total_cachet" wire:model="form.montant_total_cachet"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div class="relative">
                            <label for="modalites_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modalités de paiement</label>
                            <select id="modalites_paiement" wire:model="form.modalites_paiement"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">Sélectionner</option>
                                <option value="Avance + Solde">Avance + Solde</option>
                                <option value="Paiement unique">Paiement unique</option>
                                <option value="Autre">Autre</option>
                            </select>
                            <div class="absolute top-0 right-0 mt-1 mr-1">
                                <span class="text-gray-400 cursor-help" x-data="{ tooltipOpen: false }" @mouseover="tooltipOpen = true" @mouseleave="tooltipOpen = false">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-show="tooltipOpen" class="absolute z-10 p-2 bg-gray-800 text-white text-xs rounded shadow-lg" style="width: 200px; right: 0;">
                                        Avance + Solde : Paiement en deux parties (acompte puis solde).<br>
                                        Paiement unique : Paiement complet en une fois.<br>
                                        Autre : Modalités personnalisées.
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label for="montant_avance" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Montant de l'avance (si applicable)</label>
                            <input type="number" step="0.01" id="montant_avance" wire:model="form.montant_avance"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div>
                            <label for="date_limite_paiement_solde" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date limite de paiement du solde</label>
                            <input type="date" id="date_limite_paiement_solde" wire:model="form.date_limite_paiement_solde"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frais annexes pris en charge :</label>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="frais_annexes_transport" wire:model="form.frais_annexes_transport" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="frais_annexes_transport" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Transport</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="frais_annexes_hebergement" wire:model="form.frais_annexes_hebergement" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="frais_annexes_hebergement" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Hébergement</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="frais_annexes_restauration" wire:model="form.frais_annexes_restauration" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="frais_annexes_restauration" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Restauration</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="frais_annexes_per_diem" wire:model="form.frais_annexes_per_diem" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="frais_annexes_per_diem" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Per diem</label>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="frais_annexes_autres" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Autres frais (champ texte libre)</label>
                            <textarea id="frais_annexes_autres" wire:model="form.frais_annexes_autres" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Spécificités techniques</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="materiel_fourni_organisateur" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Matériel fourni par l'organisateur</label>
                            <textarea id="materiel_fourni_organisateur" wire:model="form.materiel_fourni_organisateur" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                        <div>
                            <label for="materiel_apporte_artiste" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Matériel apporté par l’artiste</label>
                            <textarea id="materiel_apporte_artiste" wire:model="form.materiel_apporte_artiste" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                        <div>
                            <label for="besoins_techniques" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Besoins techniques (son, lumière, scène, loge, etc.)</label>
                            <textarea id="besoins_techniques" wire:model="form.besoins_techniques" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Communication et promotion</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <label for="droits_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Droits d’image (utilisation des photos/vidéos)</label>
                            <select id="droits_image" wire:model="form.droits_image"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">Sélectionner</option>
                                <option value="Oui">Oui</option>
                                <option value="Non">Non</option>
                                <option value="À définir">À définir</option>
                            </select>
                            <div class="absolute top-0 right-0 mt-1 mr-1">
                                <span class="text-gray-400 cursor-help" x-data="{ tooltipOpen: false }" @mouseover="tooltipOpen = true" @mouseleave="tooltipOpen = false">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-show="tooltipOpen" class="absolute z-10 p-2 bg-gray-800 text-white text-xs rounded shadow-lg" style="width: 200px; right: 0;">
                                        Oui : L’organisateur peut utiliser les photos/vidéos pour promotion.<br>
                                        Non : Utilisation interdite sans accord supplémentaire.<br>
                                        À définir : Négociation future des droits.
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center mt-6">
                            <input type="checkbox" id="mention_artiste_supports_communication" wire:model="form.mention_artiste_supports_communication" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <label for="mention_artiste_supports_communication" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Mention de l’artiste sur les supports de communication ?</label>
                        </div>
                        <div class="relative">
                            <label for="interdiction_captation_audio_video" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interdiction de captation audio/vidéo ?</label>
                            <select id="interdiction_captation_audio_video" wire:model="form.interdiction_captation_audio_video"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">Sélectionner</option>
                                <option value="Oui">Oui</option>
                                <option value="Non">Non</option>
                                <option value="Partielle">Partielle</option>
                            </select>
                            <div class="absolute top-0 right-0 mt-1 mr-1">
                                <span class="text-gray-400 cursor-help" x-data="{ tooltipOpen: false }" @mouseover="tooltipOpen = true" @mouseleave="tooltipOpen = false">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-show="tooltipOpen" class="absolute z-10 p-2 bg-gray-800 text-white text-xs rounded shadow-lg" style="width: 200px; right: 0;">
                                        Oui : Interdiction totale des captations audio/vidéo.<br>
                                        Non : Captations autorisées sans restriction.<br>
                                        Partielle : Captations autorisées uniquement pour le public (ex. téléphones), interdites pour l’organisateur ou tiers sans accord.
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Clauses contractuelles</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="clause_annulation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Clause d'annulation (conditions, délais, pénalités)</label>
                            <textarea id="clause_annulation" wire:model="form.clause_annulation" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                        <div>
                            <label for="responsabilite_force_majeure" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsabilité en cas de force majeure</label>
                            <input type="text" id="responsabilite_force_majeure" wire:model="form.responsabilite_force_majeure"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                        </div>
                        <div class="relative">
                            <label for="assurance_securite_lieu_par" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assurance / sécurité du lieu assurée par</label>
                            <select id="assurance_securite_lieu_par" wire:model="form.assurance_securite_lieu_par"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="">Sélectionner</option>
                                <option value="Organisateur">Organisateur</option>
                                <option value="Artiste">Artiste</option>
                                <option value="Autre">Autre</option>
                            </select>
                            <div class="absolute top-0 right-0 mt-1 mr-1">
                                <span class="text-gray-400 cursor-help" x-data="{ tooltipOpen: false }" @mouseover="tooltipOpen = true" @mouseleave="tooltipOpen = false">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-show="tooltipOpen" class="absolute z-10 p-2 bg-gray-800 text-white text-xs rounded shadow-lg" style="width: 200px; right: 0;">
                                        Organisateur : L’organisateur assure la sécurité et l’assurance du lieu.<br>
                                        Artiste : L’artiste prend en charge ces responsabilités.<br>
                                        Autre : Un tiers (ex. société externe) est responsable.
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="engagement_ponctualite_presence" wire:model="form.engagement_ponctualite_presence" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <label for="engagement_ponctualite_presence" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Engagement de ponctualité / présence</label>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-4">

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Notes</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="observations_particulieres" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observations particulières / Notes diverses</label>
                            <textarea id="observations_particulieres" wire:model="form.observations_particulieres" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out"></textarea>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut de la prestation <span class="text-red-500">*</span></label>
                            <select id="status" wire:model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 px-3 py-2 transition-colors transition-shadow duration-200 ease-in-out">
                                <option value="en cours de redaction">En cours de rédaction</option>
                                <option value="validee">Validée</option>
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['form.status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <div class="mt-8 flex flex-col sm:flex-row justify-end items-center gap-3">

                            <button wire:click="closeModal" type="button" class="w-full sm:w-auto px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Annuler
                            </button>

                            <button type="submit"
                                    class="w-full sm:w-auto px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="savePrestation">
                                    <?php echo e($editingPrestationId ? 'Mettre à jour' : 'Enregistrer'); ?>

                                </span>

                                <span wire:loading wire:target="savePrestation">
                                    Enregistrement...
                                </span>

                            </button>
                        </div>
                        <div wire:loading class="flex items-center justify-center w-full sm:w-auto">
                            <svg class="animate-spin h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/livewire/admin/calendar/prestation/prestation-form-modal.blade.php ENDPATH**/ ?>