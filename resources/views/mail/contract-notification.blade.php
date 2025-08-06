<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Nouveau contrat à examiner</title>
    <style type="text/css">
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333333; }
        table { border-collapse: collapse; width: 100%; }
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f8f8; }
        .content { background-color: #ffffff; padding: 20px; border-radius: 4px; }
        h1 { font-size: 24px; color: #333333; margin-bottom: 20px; }
        p { margin: 0 0 10px; }
        ul { margin: 10px 0; padding-left: 20px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #3490dc; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 12px; color: #666666; text-align: center; }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 10px; }
            .content { padding: 15px; }
            h1 { font-size: 20px; }
            .button { padding: 8px 16px; }
        }
    </style>
</head>
<body>
<table class="wrapper" role="presentation">
    <tr>
        <td>
            <div class="content">
                <h1>Nouveau contrat à examiner</h1>
                <p>Bonjour,</p>
                <p>Un nouveau contrat a été envoyé pour la prestation suivante :</p>
                <ul>
                    <li><strong>Artiste</strong> : {{ $artiste }}</li>
                    <li><strong>Date</strong> : {{ $date }}</li>
                    <li><strong>Lieu</strong> : {{ $lieu }}</li>
                </ul>
                @if ($customMessage)
                    <p><strong>Message de l’équipe de Rue Joss</strong> :</p>
                    <p>{{ $customMessage }}</p>
                @endif
                <p>Veuillez examiner et signer le contrat via le lien suivant :</p>
                <p>
                    <a href="{{ $url }}" class="button">Examiner le contrat</a>
                </p>
                <p><strong>Attention</strong> : Ce lien expire le {{ $expiresAt }}.</p>
            </div>
            <div class="footer">
                <p>Merci,<br>L’équipe de Rue Joss</p>
            </div>
        </td>
    </tr>
</table>
</body>
</html>

