<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Code de Vérification (OTP)</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #4CAF50; /* A green color for a positive tone */
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px 0;
            text-align: center;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .otp-code {
            display: inline-block;
            background-color: #e0e0e0;
            color: #333;
            font-size: 32px;
            font-weight: bold;
            padding: 15px 30px;
            margin: 20px 0;
            border-radius: 6px;
            letter-spacing: 3px;
        }
        .warning {
            color: #888;
            font-size: 14px;
            margin-top: 20px;
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Code de Vérification</h1>
    </div>
    <div class="content">
        <p>Bonjour,</p>
        <p>Votre code de vérification à usage unique (OTP) est :</p>
        <div class="otp-code">
            <strong>{{ $otp }}</strong>
        </div>
        <p>Ce code est valable pendant {{ $expiryMinutes ?? 10 }} minutes.</p>
        <p>Veuillez l'utiliser pour compléter votre action.</p>
    </div>
    <div class="warning">
        <p>Si vous n'avez pas demandé ce code, veuillez ignorer cet email.</p>
        <p>Pour des raisons de sécurité, ne partagez jamais ce code avec qui que ce soit.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Event App. Tous droits réservés.</p>
    </div>
</div>
</body>
</html>
