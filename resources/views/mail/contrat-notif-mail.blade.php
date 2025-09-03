<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #FFD700; /* Jaune or */
            color: #000000;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px;
        }
        .details {
            background-color: #f9f9f9;
            border-left: 5px solid #FFD700;
            padding: 15px;
            margin: 20px 0;
        }
        .details strong {
            color: #000000;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #000000;
            color: #FFD700;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #888888;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>{{ $subject }}</h1>
    </div>
    <div class="content">
        <p>Bonjour {{ $contrat->prestation->nom_representant_legal_artiste }},</p>
        <p>{{ $notificationMessage }}</p>

        <div class="details">
            <p><strong>ID du contrat :</strong> {{ $contrat->id }}</p>
        </div>

       {{-- <div class="button-container">
            <a href="{{ url('/contrats/'.$contrat->id) }}" class="button">
                Voir le contrat
            </a>
        </div>--}}
    </div>
    <div class="footer">
        <p>Cet e-mail a été envoyé automatiquement. Veuillez ne pas y répondre.</p>
    </div>
</div>
</body>
</html>
