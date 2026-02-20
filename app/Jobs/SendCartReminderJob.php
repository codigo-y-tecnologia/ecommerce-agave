<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Carrito;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CartReminderNotification;

class SendCartReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Carrito $carrito,
        public string $stage,
        public string $type
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->resolveEmail();

        if (!$email) {
            return;
        }

        Notification::route('mail', $email)
            ->notify(
                new CartReminderNotification(
                    $this->carrito,
                    $this->stage,
                    $this->type
                )
            );

        DB::table('tbl_cart_notifications')->insert([
            'id_carrito' => $this->carrito->id_carrito,
            'canal' => 'email',
            'etapa' => $this->stage,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function resolveEmail(): ?string
    {
        if ($this->carrito->usuario) {
            return $this->carrito->usuario->vEmail;
        }

        return $this->carrito->vEmail_invitado;
    }
}
