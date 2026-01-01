<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SolicitudPostventa;

class SolicitudPostventaCliente extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SolicitudPostventa $solicitud)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recibimos tu solicitud de postventa')
            ->greeting('Hola ' . $notifiable->vNombre)
            ->line('Hemos recibido tu solicitud de ' . $this->solicitud->eTipo . '.')
            ->line('Pedido #' . $this->solicitud->id_pedido)
            ->line('Motivo que nos indicaste:')
            ->line($this->solicitud->vMotivo)
            ->line('Nuestro equipo la revisará y te notificaremos el resultado.')
            ->line('Gracias por comprar con nosotros.');
    }
}
