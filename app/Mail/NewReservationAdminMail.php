<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReservationAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Nouvelle réservation reçue ! [Code: ' . $this->reservation->code . ']',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_notification', // On crée ce fichier juste après
        );
    }
}