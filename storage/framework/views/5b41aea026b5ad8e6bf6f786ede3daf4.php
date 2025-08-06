<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Contrat de Prestation</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
        }

        /* Conteneur principal du header */
        .header {
            width: 100%;
            min-height: 120px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        /* Table pour la structure du header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            max-height: 120px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        /* Partie gauche (30%) */
        .left-box {
            width: 30%;
            text-align: left;
        }

        .left-box img {
            height: auto;
            max-height: 100px;
            max-width: 100%;
            display: block;
        }

        /* Partie centrale (30%) - Espace vide */
        .center-box {
            width: 30%;
        }

        /* Partie droite (40%) */
        .right-box {
            width: 40%;
            text-align: right;
        }

        .right-box .qr img {
            width: 100px;
            height: 100px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .date {
            font-size: 12px;
            line-height: 1.2;
            padding-top: 5px;
        }

        /* Contenu du contrat */
        .content {
            margin-top: 20px;
        }

        /* Signatures */
        .signature-wrapper {
            margin-top: 60px;
            border: 1px solid #ccc;
            padding: 15px;
            page-break-inside: avoid;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .signature-section {
            display: block;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            vertical-align: top;
            margin-right: 2%;
        }

        .signature-box:last-child {
            margin-right: 0;
        }

        .stamp {
            font-weight: bold;
            font-size: 16px;
            font-style: italic;
            padding: 15px 25px;
            border: 2px solid;
            border-radius: 8px;
            text-shadow: 1px 1px 1px #aaa;
            display: inline-block;
            white-space: nowrap;
        }

        .approved {
            color: green;
            border-color: green;
        }

        .rejected {
            color: red;
            border-color: red;
        }

        .empty {
            color: #ccc;
            border-color: #ccc;
        }

        .label {
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td class="left-box">
                <img src="<?php echo e(public_path('storage/logo.jpg')); ?>" alt="Logo" />
            </td>
            <td class="center-box">
            </td>
            <td class="right-box">
                <div class="qr">
                    <img src="<?php echo e($qrCodeSvg); ?>" alt="QR Code du contrat" />
                </div>
                <div class="date">
                    Émis le : <?php echo e($dateEmission); ?>

                </div>
            </td>
        </tr>
    </table>
</div>

<div class="content">
    <?php echo $contrat->content; ?>

</div>

<div class="signature-wrapper">
    <div class="signature-title">Signatures</div>
    <div class="signature-section">
        <div class="signature-box">
            <?php if($contrat->signature_artiste_representant === true): ?>
                <div class="stamp approved">Lu et approuvé</div>
            <?php elseif($contrat->signature_artiste_representant === false): ?>
                <div class="stamp rejected">Rejeté</div>
            <?php else: ?>
                <div class="stamp empty">&nbsp;</div>
            <?php endif; ?>
            <div class="label">Artiste / Représentant</div>
            <span style="visibility: hidden;">/signature-artiste/</span>
        </div>

        <div class="signature-box">
            <?php if($contrat->signature_contractant === true): ?>
                <div class="stamp approved">Lu et approuvé</div>
            <?php elseif($contrat->signature_contractant === false): ?>
                <div class="stamp rejected">Rejeté</div>
            <?php else: ?>
                <div class="stamp empty">&nbsp;</div>
            <?php endif; ?>
            <div class="label">Contractant</div>
            <span style="visibility: hidden;">/signature-contractant/</span>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/pdf/view_contract.blade.php ENDPATH**/ ?>