<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Http\Request;

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

        return response()->json([
            'message' => 'Réservation du service ' . $reservation->service->name . ' reçue avec succès !',
            'code' => $reservation->code, // On renvoie le code au client
            'reservation' => $reservation
        ], 201);
    }
}
