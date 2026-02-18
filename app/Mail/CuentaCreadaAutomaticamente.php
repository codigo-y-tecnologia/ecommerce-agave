<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class CuentaCreadaAutomaticamente extends Mailable
{
    use Queueable, SerializesModels;

    public Usuario $usuario;
    public string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Usuario $usuario, string $token)
    {
        $this->usuario = $usuario;
        $this->token = $token;
    }

    public function build()
    {
        return $this
            ->subject('Te creamos una cuenta en ' . config('app.name'))
            ->view('emails.cuenta_creada_automatica');
    }
}
