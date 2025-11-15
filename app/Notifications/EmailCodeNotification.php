<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailCodeNotification extends Notification
{
    use Queueable;
    public string $code;

    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Email Verification Code')
            ->greeting('Hello!')
            ->line('We received a request to verify your email address.')
            ->line('Please use the following verification code:')
            ->line('')
            ->line('🔐 **' . $this->code . '**') // يظهر بشكل بارز
            ->line('')
            ->line('If you did not request this, you can ignore this email.')
            ->salutation('Thank you for using our app!');
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
