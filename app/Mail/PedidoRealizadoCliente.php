<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class PedidoRealizadoCliente extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $subtotal;
    public $envio;
    public $descuento;
    public $totalFinal;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, $subtotal, $envio, $descuento, $totalFinal)
    {
        $this->pedido = $pedido;
        $this->subtotal = $subtotal;
        $this->envio = $envio;
        $this->descuento = $descuento;
        $this->totalFinal = $totalFinal;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Pedido Realizado Cliente',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.pedido_cliente',
    //     );
    // }

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
        return $this->subject('¡Gracias por tu compra! Pedido #' . $this->pedido->id_pedido)
            ->view('emails.pedido_cliente');
    }
}
