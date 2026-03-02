<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Réservation</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .box { border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #f9f9f9; }
        .highlight { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <h2>👋 Hello Admin,</h2>
    <p>Une nouvelle demande vient d'arriver sur la plateforme !</p>

    <div class="box">
        <h3>Détails de la mission :</h3>
        <ul>
            <li>
                <p><strong>Services commandés :</strong></p>
                <ul>
                    @foreach($reservation->services as $service)
                        <li>{{ $service->name }} ({{ $service->pivot->price_at_booking }} $)</li>
                    @endforeach
                </ul>
                <p><strong>Prix Total :</strong> {{ $reservation->total_price }} $</p>
            </li>
            <li><strong>Date :</strong> {{ \Carbon\Carbon::parse($reservation->intervention_date)->format('d/m/Y à H:i') }}</li>
            <li><strong>Lieu :</strong> {{ $reservation->address }}</li>
            <li><strong>Code Résa :</strong> <span class="highlight">{{ $reservation->code }}</span></li>
        </ul>

        <h3>Client :</h3>
        <ul>
            <li><strong>Nom :</strong> {{ $reservation->client->firstname }} {{ $reservation->client->lastname }}</li>
            <li><strong>Téléphone :</strong> {{ $reservation->client->phone }}</li>
            <li><strong>Email :</strong> {{ $reservation->client->email }}</li>
        </ul>
    </div>

    <p style="margin-top: 20px;">
        <a href="http://localhost:3000/admin/reservations" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            Accéder au Dashboard pour assigner une ménagère
        </a>
    </p>
</body>
</html>