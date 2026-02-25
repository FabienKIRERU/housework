<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de Réservation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #333; }
        .code-box { background-color: #e8f0fe; border: 2px dashed #1a73e8; color: #1a73e8; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0; border-radius: 5px; }
        .details { margin-top: 20px; }
        .details p { margin: 5px 0; color: #555; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Merci {{ $reservation->client->firstname }} !</h2>
            <p>Votre demande de service a bien été reçue.</p>
        </div>

        <p>Voici votre code de suivi unique. <strong>Conservez-le précieusement</strong>, il vous servira de mot de passe pour modifier ou suivre votre commande.</p>

        <div class="code-box">
            {{ $reservation->code }}
        </div>

        <div class="details">
            <h3>Détails de l'intervention :</h3>
            <p>                
                <p><strong>Services commandés :</strong></p>
                <ul>
                    @foreach($reservation->services as $service)
                        <li>{{ $service->name }} ({{ $service->pivot->price_at_booking }} $)</li>
                    @endforeach
                </ul>            
            </p>
            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($reservation->intervention_date)->format('d/m/Y à H:i') }}</p>
            <p><strong>Adresse :</strong> {{ $reservation->address }}</p>
            <p><strong>Statut :</strong> <span style="color: orange;">En attente de validation</span></p>
        </div>

        <div class="footer">
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
            <p>L'équipe HouseWork</p>
        </div>
    </div>
</body>
</html>