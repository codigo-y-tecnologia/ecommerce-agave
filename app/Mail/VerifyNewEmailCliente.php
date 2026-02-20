<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class VerifyNewEmailCliente extends Mailable
{
    use Queueable, SerializesModels;

    public Usuario $user;
    public string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Usuario $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Confirma tu nuevo correo electrónico')
            ->view('emails.verify-new-email')
            ->with([
                'nombre' => $this->user->vNombre,
                'verificationUrl' => route('perfil.verifyEmail', $this->token),
            ]);
    }
}
