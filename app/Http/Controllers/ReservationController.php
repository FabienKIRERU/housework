<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\TrackReservationRequest;
use App\Mail\NewReservationAdminMail;
use App\Mail\ReservationCreatedMail;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    private ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function store(StoreReservationRequest $request)
    {        
        // Le repository gère toute la logique (création client + code unique)
        $reservation = $this->reservationRepository->createReservationFromClient($request->validated());

        // 2. ENVOI DES EMAILS 📧
        try {
            // On charge les relations pour les vues
            $reservation->load(['client', 'services']);
            
            // A. Email Client
            Mail::to($reservation->client->email)
                ->send(new ReservationCreatedMail($reservation));
            
            // B. Email Admin (Nouveau !)
            // On récupère l'email depuis le .env, sinon on met une valeur par défaut
            $adminEmail = env('ADMIN_EMAIL', 'admin@housework.com');
            
            Mail::to($adminEmail)
                ->send(new NewReservationAdminMail($reservation));
            
        } catch (\Exception $e) {
            // Log::error("Erreur d'envoi mail : " . $e->getMessage());
        }

        // On récupère les noms des services et on les colle avec une virgule
        $serviceNames = $reservation->services->pluck('name')->implode(', ');
        
        return response()->json([
            'message' => 'Réservation des services ' . $serviceNames . ' reçue avec succès !',
            'code' => $reservation->code, // On renvoie le code au client
            'reservation' => $reservation
        ], 201);
    }


    public function track(TrackReservationRequest $request)
    {
        // On appelle la nouvelle méthode flexible
        $reservation = $this->reservationRepository->findClientReservation(
            $request->code,
            $request->email,    // Peut être null
            $request->phone,    // Peut être null
            // $request->name  // Peut être null
        );

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable ou informations incorrectes.'], 404);
        }

        // Cas annulé
        if ($reservation->status === 'cancelled') {
            return response()->json([
                'status' => 'cancelled',
                'message' => 'Cette réservation a été annulée.',
                'details' => $reservation // On renvoie quand même les infos pour historique
            ]);
        }

        // On formate un peu la réponse pour le Frontend
        return response()->json([
            'message' => 'Réservation trouvée.',
            'reservation' => $reservation,
            // Petit helper pour le frontend : est-ce qu'on a une ménagère ?
            'has_houseworker' => $reservation->houseworker_id !== null, 
        ]);
    }
}
