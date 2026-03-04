<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class PedidoNuevoAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $snapshot;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, $snapshot)
    {
        $this->pedido = $pedido;
        $this->snapshot = $snapshot;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Nuevo pedido recibido #' . $this->pedido->id_pedido)
            ->view('emails.pedido_admin');
    }
}
