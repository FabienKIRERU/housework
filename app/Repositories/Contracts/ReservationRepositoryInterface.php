<?php

namespace App\Repositories\Contracts;
use App\Models\Reservation;


interface ReservationRepositoryInterface 
{
    // On passe toutes les données (client + résa) d'un coup
    public function createReservationFromClient(array $data);

    // Remplace "findByCodeAndEmail" par :
    public function findClientReservation(string $code, ?string $email = null, ?string $phone = null);


    public function getAllReservations();
    public function updateStatus($id, $status);
    public function assignHouseworker($id, $houseworkerId);

    // Récupérer la liste complète (avec filtres possibles plus tard)
    public function getAdminReservations(array $filters = []);
    // Modifier une réservation (Assignation ou Changement de statut)
    public function adminUpdateReservation($id, array $data);
}