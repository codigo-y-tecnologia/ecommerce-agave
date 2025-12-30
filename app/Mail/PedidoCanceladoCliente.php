<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class PedidoCanceladoCliente extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Pedido $pedido,
        public string $motivo
    ) {}

    public function build()
    {
        return $this
            ->subject('Tu pedido fue cancelado y reembolsado')
            ->view('emails.pedido_cancelado');
    }
}
