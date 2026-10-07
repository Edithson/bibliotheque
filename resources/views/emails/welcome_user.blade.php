<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue à La Bibliothèque des Mots</title>
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
            font-size: 26px;
            font-family: 'Cinzel', Georgia, serif;
        }
        .header p {
            color: #d69e2e;
            font-style: italic;
            margin: 0;
            font-size: 15px;
        }
        .content {
            font-size: 16px;
            line-height: 1.7;
            color: #f7fafc;
        }
        .highlight-box {
            background-color: #1c0d06;
            border-left: 4px solid #e9c96b;
            padding: 18px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .highlight-box h3 {
            margin: 0 0 8px 0;
            color: #e9c96b;
            font-size: 17px;
        }
        .highlight-box ul {
            margin: 0;
            padding-left: 20px;
            color: #e2e8f0;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 20px 0;
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
            <h1>📚 La Bibliothèque des Mots</h1>
            <p>Bienvenue parmi les passionnés d'écrits & d'ouvrages numériques</p>
        </div>

        <div class="content">
            <p>Cher(e) <strong>{{ $user->name }}</strong>,</p>

            <p>
                C'est un immense privilège de vous accueillir au sein du sanctuaire de <strong>La Bibliothèque des Mots</strong>. Votre compte d'accès lecteur a été configuré avec succès dans nos registres.
            </p>

            <div class="highlight-box">
                <h3>📖 Les privilèges de votre espace personnel :</h3>
                <ul>
                    <li>Consulter notre collection variée d'ouvrages numériques (PDF, e-books).</li>
                    <li>Accéder gratuitement aux œuvres fondamentales libres de droit.</li>
                    <li>Retrouver l'historique complet de vos lectures et téléchargements dans votre espace <em>« Mes Livres »</em>.</li>
                </ul>
            </div>

            <p>
                Parcourez dès aujourd'hui les rayons de notre bibliothèque et plongez au cœur de récits captivants.
            </p>

            <div class="btn-container">
                <a href="{{ route('shop.index') }}" class="btn">✨ Découvrir le Registre des Livres</a>
            </div>
        </div>

        <div class="footer">
            Cordialement,<br>
            <strong>Le Bibliothécaire — La Bibliothèque des Mots</strong><br>
            <span style="font-size: 11px;">Cet email vous a été transmis suite à la création de votre compte sur notre plateforme.</span>
        </div>
    </div>
</body>
</html>
