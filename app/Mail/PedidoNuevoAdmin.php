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
    public $subtotal;
    public $envio;
    public $descuento;
    public $totalFinal;
    public $cupon;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, $subtotal, $envio, $descuento, $totalFinal, $cupon = null)
    {
        $this->pedido = $pedido;
        $this->subtotal = $subtotal;
        $this->envio = $envio;
        $this->descuento = $descuento;
        $this->totalFinal = $totalFinal;
        $this->cupon = $cupon;
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
