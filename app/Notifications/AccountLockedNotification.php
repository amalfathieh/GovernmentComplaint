<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountLockedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail']; // أو FCM إذا عندك Push
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('محاولات دخول غير ناجحة')
            ->line('تم قفل حسابك مؤقتًا بسبب محاولات دخول فاشلة.')
            ->line('يمكنك المحاولة مرة أخرى بعد ' . $notifiable->locked_until->format('H:i'));
    }
}
