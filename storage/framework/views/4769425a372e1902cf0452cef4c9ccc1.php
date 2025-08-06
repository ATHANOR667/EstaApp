<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de Prestation</title>
</head>
<body>
<div style="font-family: sans-serif; line-height: 1.6; color: #333;">

    <p>Cher/chère <?php echo e($contrat->prestation->nom_structure_contractante); ?>,</p>

    <p>
        Veuillez trouver ci-joint le contrat de prestation concernant votre événement.
        Ce document a été validé et signé par le représentant de l'artiste.
    </p>

    <p>
        Afin de finaliser ce contrat, nous vous invitons à le consulter et à le valider en cliquant sur le bouton ci-dessous.
        Vous aurez la possibilité de l'approuver ou de le rejeter directement en ligne.
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="<?php echo e($signedUrl); ?>" style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Consulter et valider le contrat
        </a>
    </div>

    <p>
        Ce lien est sécurisé et expire dans les 48 heures.
        Il deviendra invalide dès que vous aurez pris votre décision.
    </p>

    <p>Cordialement,</p>
    <p>L'équipe de Rue Joss</p>

</div>
</body>
</html>
<?php /**PATH C:\Users\MARCAU\PhpstormProjects\EstaApp\resources\views/mail/contract-mail.blade.php ENDPATH**/ ?>