<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Mission</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .mission-box { background-color: #f0fff4; border: 1px solid #c6f6d5; padding: 20px; border-radius: 8px; }
        .info-label { font-weight: bold; color: #2f855a; }
        .client-info { margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <h2>Bonjour {{ $reservation->houseworker->firstname }},</h2>
    <p>L'administrateur vous a assigné une nouvelle mission !</p>

    <div class="mission-box">
        <h3>📅 Détails de l'intervention</h3>
        <p>
            <span class="info-label">Date et Heure :</span><br>
            {{ \Carbon\Carbon::parse($reservation->intervention_date)->format('d/m/Y à H:i') }}
        </p>
        <p>
            <span class="info-label">Service :</span><br>
            {{ $reservation->service->name }}
        </p>
        <p>
            <span class="info-label">Lieu :</span><br>
            {{ $reservation->address }}
        </p>
    </div>

    <div class="client-info">
        <h3>👤 Contact Client</h3>
        <p>
            <strong>Nom :</strong> {{ $reservation->client->firstname }} {{ $reservation->client->lastname }}<br>
            <strong>Téléphone :</strong> <a href="tel:{{ $reservation->client->phone }}">{{ $reservation->client->phone }}</a>
        </p>
    </div>

    <p>Bon courage !</p>
</body>
</html>