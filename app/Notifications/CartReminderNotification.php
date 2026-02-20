<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Carrito;

class CartReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Carrito $carrito,
        private string $stage,
        private string $type
    ) {
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
        return match ($this->type) {
            'soft' => $this->softReminder(),
            'incentive' => $this->incentiveReminder(),
            'final' => $this->finalReminder(),
        };
    }

    private function softReminder()
    {
        return (new MailMessage)
            ->subject('Tu carrito sigue esperándote')
            ->line('Notamos que dejaste productos en tu carrito.')
            ->action('Continuar compra', url('/carrito'));
    }

    private function incentiveReminder()
    {
        return (new MailMessage)
            ->subject('Un incentivo especial para ti')
            ->line('Finaliza tu compra y aprovecha este beneficio exclusivo.')
            ->action('Aprovechar ahora', url('/carrito'));
    }

    private function finalReminder()
    {
        return (new MailMessage)
            ->subject('Última oportunidad')
            ->line('Tu carrito está a punto de expirar.')
            ->action('Finalizar compra', url('/carrito'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
