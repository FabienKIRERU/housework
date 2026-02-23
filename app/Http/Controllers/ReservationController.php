<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ReservationCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewReservationAdminMail;
use App\Http\Requests\StoreReservationRequest;
use App\Repositories\Contracts\ReservationRepositoryInterface;

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
            $reservation->load(['client', 'service']);
            
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
        
        return response()->json([
            'message' => 'Réservation du service ' . $reservation->service->name . ' reçue avec succès !',
            'code' => $reservation->code, // On renvoie le code au client
            'reservation' => $reservation
        ], 201);
    }
}
