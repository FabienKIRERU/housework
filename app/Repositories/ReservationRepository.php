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

    /**
     * Recherche flexible pour le client (Code + Email/Tel/Nom)
     */
    public function findClientReservation(string $code, ?string $email = null, ?string $phone = null, ?string $name = null){
        return Reservation::where('code', $code)
            ->whereHas('client', function ($query) use ($email, $phone) {
                // On regroupe les conditions dans un "AND ( ... OR ... OR ... )"
                $query->where(function ($q) use ($email, $phone) {
                    if ($email) {
                        $q->orWhere('email', $email);
                    }
                    if ($phone) {
                        $q->orWhere('phone', $phone);
                    }
                });
            })
            // ON CHARGE LES INFOS COMPLÈTES
            ->with([
                'service',       // Le détail du service
                'client',        // Le détail du client
                // Sécurité : On ne donne que le Prénom et Nom de la ménagère, pas son email/mdp
                'houseworker:id,firstname,name,phone' 
            ])
            ->first();
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

    
    /**
     * ADMIN : Liste avec Filtres (Statut) et Recherche (Matricule)
     */
    public function getAdminReservations(array $filters = [])
    {
        $query = Reservation::with(['client', 'service', 'houseworker'])
            ->orderBy('created_at', 'desc');

        // 1. Filtre par Statut (ex: ?status=pending)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 2. Recherche par Matricule/Code (ex: ?search=KAB2026...)
        if (!empty($filters['search'])) {
            $query->where('code', 'like', '%' . $filters['search'] . '%');
            // Optionnel : Tu pourrais aussi chercher par nom du client ici avec un orWhereHas...
        }

        // On retourne le résultat (on pourrait ajouter ->paginate(20) plus tard)
        return $query->get();
    }

    
    public function adminUpdateReservation($id, array $data)
    {
        $reservation = Reservation::findOrFail($id);

        // On vérifie si la date d'intervention prévue est passée de plus de 72h.
        $interventionDate = Carbon::parse($reservation->intervention_date);
        $now = Carbon::now();

        // Si la date est passée ET qu'il s'est écoulé plus de 72h
        if ($interventionDate->isPast() && $interventionDate->diffInHours($now) > 72) {
            throw new \Exception("Modification interdite : Cette réservation est archivée (date d'intervention passée de plus de 72h).", 403);
        }


         // Si l'admin essaie de changer la date, elle ne doit pas être dans le passé.
        if (isset($data['intervention_date'])) {
            $newDate = Carbon::parse($data['intervention_date']);
            if ($newDate->isPast()) {
                throw new \Exception("Erreur : Vous ne pouvez pas déplacer une intervention à une date passée.", 422);
            }
        }

        // Si la réservation est déjà finie ou annulée, on ne touche plus à rien.
        if (in_array($reservation->status, ['cancelled', 'completed'])) {
            throw new \Exception("Impossible de modifier une réservation terminée ou annulée.", 400);
        }


        // On regarde quel sera le futur statut (soit celui envoyé, soit l'actuel)
        $futureStatus = $data['status'] ?? $reservation->status;


        // On regarde qui sera la future ménagère (soit celle envoyée, soit l'actuelle)
        // Attention : on vérifie si la clé existe dans $data, car l'admin peut envoyer null pour retirer la ménagère
        $futureHouseworkerId = array_key_exists('houseworker_id', $data) 
                                ? $data['houseworker_id'] 
                                : $reservation->houseworker_id;
        
        
        // LE TEST FATIDIQUE :
        // Si on veut être 'confirmed' ou 'completed', il FAUT une ménagère.
        if (in_array($futureStatus, ['confirmed', 'completed']) && empty($futureHouseworkerId)) {
            throw new \Exception("Action refusée : Impossible de confirmer ou terminer une réservation sans assigner une ménagère.", 422);
        }



        // LOGIQUE MÉTIER :
        // Si on assigne une ménagère et que le statut est encore 'pending',
        // on passe automatiquement à 'confirmed' pour gagner du temps.
        if (isset($data['houseworker_id']) && $data['houseworker_id'] != null && $reservation->status === 'pending') {
            $data['status'] = 'confirmed';
        }

        // Mise à jour des données
        $reservation->update($data);

        // On retourne l'objet rafraîchi avec ses relations (pour que le Frontend React mette à jour l'affichage)
        return $reservation->load(['houseworker', 'client', 'service']);
    }
}