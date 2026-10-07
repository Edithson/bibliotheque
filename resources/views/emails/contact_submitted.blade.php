<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
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
            border: 2px solid #8b5cf6;
            border-color: #e9c96b;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        .header {
            border-bottom: 2px solid #4a2c17;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            color: #e9c96b;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            color: #d69e2e;
            font-style: italic;
            margin: 0;
            font-size: 14px;
        }
        .info-row {
            margin-bottom: 12px;
            font-size: 15px;
            line-height: 1.5;
        }
        .label {
            color: #e9c96b;
            font-weight: bold;
        }
        .message-box {
            background-color: #1c0d06;
            border-left: 4px solid #e9c96b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #f7fafc;
            white-space: pre-line;
            font-size: 15px;
        }
        .footer {
            border-top: 1px solid #4a2c17;
            padding-top: 15px;
            margin-top: 25px;
            text-align: center;
            font-size: 13px;
            color: #a0aec0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #d69e2e, #b7791f);
            color: #1a0f0a;
            font-weight: bold;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 La Bibliothèque des Mots</h1>
            <p>Nouveau message de correspondance reçu</p>
        </div>

        <div class="info-row">
            <span class="label">Expéditeur :</span> {{ $contact->name }} (&lt;a href="mailto:{{ $contact->email }}" style="color: #63b3ed;">{{ $contact->email }}</a>&gt;)
        </div>

        <div class="info-row">
            <span class="label">Sujet :</span> {{ $contact->subject_label }}
        </div>

        <div class="info-row">
            <span class="label">Date :</span> {{ $contact->created_at?->format('d/m/Y à H:i') ?? now()->format('d/m/Y à H:i') }}
        </div>

        <div class="label" style="margin-top: 20px;">Contenu du message :</div>
        <div class="message-box">
            {{ $contact->message }}
        </div>

        <div style="text-align: center;">
            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn">Consulter dans le Bureau d'Administration</a>
        </div>

        <div class="footer">
            Cet email a été envoyé automatiquement depuis le formulaire de contact de <strong>La Bibliothèque des Mots</strong>.
        </div>
    </div>
</body>
</html>
