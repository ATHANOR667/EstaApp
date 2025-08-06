<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Profil Administrateur</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 10mm;
            font-size: 9pt;
            color: #333;
        }
        h1, h2, h3 {
            color: #1a202c;
            margin-bottom: 8px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 1.8em;
            margin-bottom: 4px;
        }
        .section-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 5px;
            color: #2d3748;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-grid-row {
            display: table-row;
        }
        .info-item {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
            width: 50%;
        }
        .info-item label {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            color: #4a5568;
        }
        .info-item span {
            display: block;
            color: #1a202c;
            font-size: 0.95em;
        }
        .roles-list, .permissions-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .roles-list li {
            font-weight: bold;
            margin-bottom: 3px;
            color: #3182ce;
        }
        .permissions-list {
            margin-left: 15px;
            font-size: 0.85em;
        }
        .permissions-list li {
            margin-bottom: 1px;
            color: #4a5568;
        }
        .image-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .image-container img {
            max-width: 120px;
            max-height: 120px;
            border-radius: 50%;
            border: 2px solid #a0aec0;
            padding: 3px;
            object-fit: cover;
        }
        .id-images-wrapper {
            display: block;
            text-align: center;
            margin-bottom: 15px;
            margin-top: 20px;
        }
        .id-image-container {
            display: inline-block;
            vertical-align: top;
            margin: 0 5px;
            text-align: center;
            width: 48%;
            box-sizing: border-box;
        }
        .id-image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            border: 1px solid #a0aec0;
            padding: 3px;
        }
        .qr-code-container {
            text-align: center;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            background-color: #f7fafc;
            border-radius: 8px;
        }
        .qr-code-container img {
            width: 120px;
            height: 120px;
            margin-bottom: 8px;
        }
        .page-break {
            page-break-after: always;
        }
        .signature-section {
            margin-top: 40px;
            text-align: right;
            padding-right: 20px;
        }
        .signature-line {
            border-top: 1px solid #888;
            width: 200px;
            margin-left: auto;
            margin-top: 30px;
        }
        .signature-text {
            font-size: 0.9em;
            color: #555;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Fiche de Profil Administrateur</h1>
        <p style="font-size: 1.1em; font-weight: bold; color: #4299e1;"><?php echo e($admin->prenom); ?> <?php echo e($admin->nom); ?></p>
        <p style="font-size: 0.85em; color: #718096;">Matricule: <?php echo e($admin->matricule); ?></p>
        <p style="font-size: 0.75em; color: #718096;">Généré le: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i')); ?></p>
    </div>

    <?php if($admin): ?>
        <div class="image-container">
            <?php if($photoProfilPath): ?>
                <img src="data:image/jpeg;base64,<?php echo e(base64_encode(file_get_contents($photoProfilPath))); ?>" alt="Photo de Profil">
            <?php else: ?>
                <div style="width: 120px; height: 120px; background-color: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5em; color: #a0aec0; font-weight: bold; margin: 0 auto;">
                    <?php echo e(strtoupper(substr($admin->prenom, 0, 1))); ?><?php echo e(strtoupper(substr($admin->nom, 0, 1))); ?>

                </div>
            <?php endif; ?>
        </div>

        <div class="section-title">Informations Générales</div>
        <div class="info-grid">
            <div class="info-grid-row">
                <div class="info-item">
                    <label>Email:</label>
                    <span><?php echo e($admin->email ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <label>Téléphone:</label>
                    <span><?php echo e($admin->telephone ?? 'N/A'); ?></span>
                </div>
            </div>
            <div class="info-grid-row">
                <div class="info-item">
                    <label>Pays:</label>
                    <span><?php echo e($admin->pays ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <label>Ville:</label>
                    <span><?php echo e($admin->ville ?? 'N/A'); ?></span>
                </div>
            </div>
            <div class="info-grid-row">
                <div class="info-item">
                    <label>Mot de Passe:</label>
                    <span><?php echo e($admin->password ? '********' : 'Non défini'); ?></span>
                </div>
                <div class="info-item">
                    <label>Passcode:</label>
                    <span><?php echo e($admin->passcode ? '********' : 'Non défini'); ?></span>
                </div>
            </div>
            <div class="info-grid-row">
                <div class="info-item">
                    <label>Dernier changement mot de passe:</label>
                    <span><?php echo e($admin->password_changed_at ? $admin->password_changed_at->format('d/m/Y H:i') : 'Jamais'); ?></span>
                </div>
                <div class="info-item">
                    <label>Dernier changement passcode:</label>
                    <span><?php echo e($admin->passcode_reset_date ? $admin->passcode_reset_date->format('d/m/Y H:i') : 'Jamais'); ?></span>
                </div>
            </div>
        </div>

        <div class="section-title">Rôles et Permissions Attribués</div>
        <?php if($admin->roles->isNotEmpty()): ?>
            <ul class="roles-list">
                <?php $__currentLoopData = $admin->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span style="color: #3182ce;"><?php echo e($role->name); ?></span>
                        <?php if($role->permissions->isNotEmpty()): ?>
                            <ul class="permissions-list">
                                <?php $__currentLoopData = $role->permissions->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>- <?php echo e($permission->name); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p style="margin-left: 15px; font-size: 0.8em; color: #718096;">Aucune permission associée à ce rôle.</p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php else: ?>
            <p>Aucun rôle attribué.</p>
        <?php endif; ?>

        
        

        <div class="page-break"></div>

        
        <div class="section-title" style="margin-top: 0;">Pièces d'Identité</div>
        <div class="id-images-wrapper">
            <div class="id-image-container">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Recto:</label>
                <?php if($pieceIdentiteRectoPath): ?>
                    <img src="data:image/jpeg;base64,<?php echo e(base64_encode(file_get_contents($pieceIdentiteRectoPath))); ?>" alt="Pièce d'Identité Recto">
                <?php else: ?>
                    <p style="font-size: 0.85em; color: #718096;">Aucun Recto</p>
                <?php endif; ?>
            </div>
            <div class="id-image-container">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Verso:</label>
                <?php if($pieceIdentiteVersoPath): ?>
                    <img src="data:image/jpeg;base64,<?php echo e(base64_encode(file_get_contents($pieceIdentiteVersoPath))); ?>" alt="Pièce d'Identité Verso">
                <?php else: ?>
                    <p style="font-size: 0.85em; color: #718096;">Aucun Verso</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="qr-code-container">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Code QR du Matricule:</label>
            <img src="<?php echo e($qrCodeBase64); ?>" alt="Code QR du Matricule">
            <p style="margin-top: 8px; font-size: 0.85em; color: #718096;">Matricule: <?php echo e($admin->matricule); ?></p>
        </div>

    <?php else: ?>
        <p style="text-align: center; color: #888;">Les informations de l'administrateur ne sont pas disponibles.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/pdf/admin_profile.blade.php ENDPATH**/ ?>