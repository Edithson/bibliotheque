<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de Mot de Passe</title>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background-color: #1a0f0a;
            color: #fbd38d;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #2b170c;
            border: 2px solid #e9c96b;
            border-radius: 8px;
            padding: 35px 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
        }
        .header {
            border-bottom: 2px solid #4a2c17;
            padding-bottom: 20px;
            margin-bottom: 25px;
            text-align: center;
        }
        .header h1 {
            color: #e9c96b;
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        .header p {
            color: #d69e2e;
            font-style: italic;
            margin: 0;
            font-size: 14px;
        }
        .content {
            font-size: 16px;
            line-height: 1.7;
            color: #f7fafc;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 25px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #fbd38d, #ed8936 50%, #c05621);
            color: #1a0f0a;
            font-weight: bold;
            font-size: 16px;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .notice {
            background-color: #1c0d06;
            border: 1px solid #4a2c17;
            padding: 15px;
            border-radius: 4px;
            font-size: 13px;
            color: #cbd5e0;
            margin-top: 20px;
        }
        .footer {
            border-top: 1px solid #4a2c17;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            font-size: 13px;
            color: #a0aec0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 La Bibliothèque des Mots</h1>
            <p>Demande de réinitialisation de mot de passe</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

            <p>
                Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte d'accès à <strong>La Bibliothèque des Mots</strong>.
            </p>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">🗝️ Réinitialiser mon mot de passe</a>
            </div>

            <p class="notice">
                ⏱️ Ce lien de réinitialisation expire dans <strong>{{ $count }} minutes</strong>.<br>
                Si vous n'avez pas demandé cette réinitialisation, aucune action supplémentaire n'est requise ; votre mot de passe actuel demeure inchangé et sécurisé.
            </p>
        </div>

        <div class="footer">
            Cordialement,<br>
            <strong>Le Bibliothécaire — La Bibliothèque des Mots</strong>
        </div>
    </div>
</body>
</html>
