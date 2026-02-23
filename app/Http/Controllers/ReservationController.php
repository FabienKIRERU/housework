<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ReservationCreatedMail;
use Illuminate\Support\Facades\Mail;
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

        // 2. ENVOI DE L'EMAIL AU CLIENT 📧
        // On enveloppe ça dans un try/catch pour ne pas planter l'API si le serveur mail échoue
        try {
            // On charge la relation 'client' et 'service' pour être sûr qu'elles soient dispo dans le mail
            $reservation->load(['client', 'service']);
            
            Mail::to($reservation->client->email)->send(new ReservationCreatedMail($reservation));
            
        } catch (\Exception $e) {
            // En production, on loggerait l'erreur ici : Log::error($e->getMessage());
            // On ne bloque pas la réponse, l'important c'est que la résa soit créée.
        }
        
        return response()->json([
            'message' => 'Réservation du service ' . $reservation->service->name . ' reçue avec succès !',
            'code' => $reservation->code, // On renvoie le code au client
            'reservation' => $reservation
        ], 201);
    }
}
