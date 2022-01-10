<?php

namespace App\Broadcasting;

use App\Fonnte;
use App\User;
use Illuminate\Notifications\Notification;

class WhatsappChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $notifiable
     * @return array|bool
     */
    public function join(User $notifiable)
    {
        //
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsapp($notifiable);

        (new Fonnte)->to($notifiable->whatsapp)
            ->send($message);
    }
}
