<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Service;
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
    public Service $service;

    public function __construct(Reservation $reservation, Service $service)
    {
        $this->reservation = $reservation;
        $this->service = $service;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🧹 Nouvelle mission : ' . $this->service->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.houseworker_mission',
        );
    }
}