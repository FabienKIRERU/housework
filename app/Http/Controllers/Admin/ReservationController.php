<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTaskRequest;
use App\Http\Requests\Admin\UpdateReservationRequest;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @group Gestion Réservations (Admin)
 * Traitement des demandes : Assignation, confirmation, annulation.
 */
class ReservationController extends Controller
{
    private ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Voir toutes les réservations
     * 
     * Affiche la liste complète triée par date décroissante.
     */
    /**
     * Voir toutes les réservations (avec filtres)
     */
    public function index(Request $request)
    {
        // On récupère uniquement les filtres intéressants
        $filters = $request->only(['status', 'search']);

        $reservations = $this->reservationRepository->getAdminReservations($filters);
        
        return response()->json($reservations);
    }

    /**
     * Traiter une réservation
     * 
     * Permet d'assigner une ménagère (ce qui confirme la résa) ou de changer le statut (terminée/annulée).
     */
    public function update(UpdateReservationRequest $request, $id)
    {
        try {
            // On essaie de faire la mise à jour via le Repository
            $reservation = $this->reservationRepository->adminUpdateReservation(
                $id, 
                $request->validated()
            );

            // LOGIQUE MAIL (qu'on a faite avant)
            if ($request->has('houseworker_id') && $reservation->houseworker) {
                 try {
                    \Illuminate\Support\Facades\Mail::to($reservation->houseworker->email)
                        ->send(new \App\Mail\HouseworkerAssignedMail($reservation));
                } catch (\Exception $e) {}
            }

            return response()->json([
                'message' => 'Réservation mise à jour avec succès.',
                'reservation' => $reservation
            ]);

        } catch (\Exception $e) {
            // Si le Repository jette une erreur (ex: Pas de ménagère), on la renvoie ici
            // Le code 422 est standard pour "Unprocessable Entity" (Erreur logique)
            // Le code 400 est pour "Bad Request"
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Assigner une ménagère à une tâche spécifique
     */
    public function assignTask(AssignTaskRequest $request, $id)
    {
        // 1. Assignation via Repository
        $reservation = $this->reservationRepository->assignHouseworkerToTask(
            $id,
            $request->service_id,
            $request->houseworker_id
        );

        // 2. Récupérer la ménagère et le service pour l'email
        $houseworker = \App\Models\User::find($request->houseworker_id);
        $service = \App\Models\Service::find($request->service_id);

        // 3. Envoi de l'email à la ménagère concernée 📧
        try {
            // On crée un Mailable spécifique ou on adapte l'ancien
            // (Il faut passer le service spécifique au Mailable maintenant)
             \Illuminate\Support\Facades\Mail::to($houseworker->email)
                 ->send(new \App\Mail\HouseworkerAssignedMail($reservation, $service));
        } catch (\Exception $e) {}

        return response()->json([
            'message' => 'Ménagère assignée à la tâche avec succès.',
            'reservation' => $reservation
        ]);
    }

}