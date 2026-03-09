<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SolicitudPostventa;

class SolicitudPostventaCreada extends Notification
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva solicitud de postventa')
            ->greeting('Hola 👋')
            ->line('Se ha creado una nueva solicitud de postventa.')
            ->line('Tipo: ' . ucfirst($this->solicitud->eTipo))
            ->line('Pedido #' . $this->solicitud->id_pedido)
            ->line('Motivo:')
            ->line($this->solicitud->vMotivo)
            ->action(
                'Ver solicitud',
                route('admin.postventa.show', $this->solicitud->id_solicitud)
            )
            ->line('Este mensaje fue generado automáticamente.');
    }

    /**
     * 🗄️ Base de datos (campanita admin)
     */
    public function toDatabase($notifiable)
    {
        return [
            'id_solicitud' => $this->solicitud->id_solicitud,
            'id_pedido' => $this->solicitud->id_pedido,
            'tipo' => $this->solicitud->eTipo,
            'motivo' => $this->solicitud->vMotivo,
        ];
    }
}
