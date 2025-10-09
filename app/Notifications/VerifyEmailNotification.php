<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Usuario;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public $user;
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(Usuario $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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

        $verificationUrl = url("/verify-email/{$this->token}");

        return (new MailMessage)
            ->subject('Verifica tu dirección de correo electrónico - Ecommerce Agave')
            ->greeting('¡Bienvenido ' . $this->user->vNombre . '!')
            ->line('Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.')
            ->action('Verificar Email', $verificationUrl)
            ->line('Si no creaste una cuenta, no es necesario realizar ninguna acción.')
            ->salutation('Saludos, El equipo de Ecommerce Agave');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
