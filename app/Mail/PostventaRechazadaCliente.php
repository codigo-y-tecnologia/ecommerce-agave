<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SolicitudPostventa;

class PostventaRechazadaCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public SolicitudPostventa $solicitud) {}

    public function build()
    {
        return $this
            ->subject('Solicitud de postventa rechazada')
            ->view('emails.postventa_rechazada');
    }
}
