<?php

namespace App\Repositories;


use Carbon\Carbon;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ReservationRepositoryInterface;

class ReservationRepository implements ReservationRepositoryInterface
{
    // On passe toutes les données (client + résa) d'un coup
    public function createReservationFromClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Création du client
            $client = Client::create([
                'name' => $data['client_name'],
                'firstname' => $data['client_firstname'],
                'email' => $data['client_email'],
                'phone' => $data['client_phone'],
            ]);


            // Génération du Code Unique
            $code = $this->generateReservationCode($data['client_name']);

            // Création de la réservation liée au client
            return Reservation::create([
                'code' => $code,
                'client_id' => $client->id,
                'service_id' => $data['service_id'],
                'intervention_date' => $data['intervention_date'],
                'address' => $data['address'],
                'status' => 'pending', // statut par défaut
            ]);

        });
    }

    private function generateReservationCode(string $name) : string 
    {
        // 3 premières lettres du nom en majuscule (ex: DUP)
        // Si le nom est court (ex: Li), on prend tout
        $prefix = strtoupper(substr($name, 0, 3));
        
        // Date : AnnéeMoisJourMinute (ex: 2024010112)
        $date = Carbon::now()->format('Ymdi');
        
        do {
            // 3 lettres aléatoires majuscules
            $random = Str::upper(Str::random(3));
            
            // Assemblage
            $code = $prefix . $date . $random;
            
            // Vérification unicité (au cas où, même si c'est improbable)
        } while (Reservation::where('code', $code)->exists());

        return $code;
    }

    public function getAllReservations()
    {
        return Reservation::with(['client', 'service', 'houseworker'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateStatus($id, $status)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $status]);
        return $reservation;
    }

    public function assignHouseworker($id, $houseworkerId)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'houseworker_id' => $houseworkerId,
            'status' => 'confirmed' // On passe en confirmé quand quelqu'un est assigné
        ]);
        return $reservation;
    }
}