<?php

namespace App\Notifications;

use App\Broadcasting\WhatsappChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageWhatsappNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message_id)
    {
        $this->message_id = $message_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WhatsappChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toWhatsapp($notifiable)
    {
        return __('Hello!') . '\n\n' . __('A new message has been received on Pesanonim') . '. ' . __('Check it out') . ': ' . url('/' . $notifiable->id . '#message-' . $this->message_id);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
