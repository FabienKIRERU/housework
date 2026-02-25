<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HouseworkerAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function envelope(): Envelope
    {
        // 1. On récupère juste la colonne 'name' de tous les services
        // 2. On les colle avec une virgule et un espace
        $serviceNames = $this->reservation->services->pluck('name')->implode(', ');

        return new Envelope(
            // On limite la taille à 50 caractères pour éviter un objet de mail kilomètrique
            // si le client a pris 10 services.
            subject: '🧹 Nouvelle mission : ' . \Illuminate\Support\Str::limit($serviceNames, 50),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.houseworker_mission',
        );
    }
}