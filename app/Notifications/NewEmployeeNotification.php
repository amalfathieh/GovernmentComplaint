<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEmployeeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
        //
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
        $dashboardUrl = config('app.dashboard_url', 'https://dashboard.yourapp.com/login');

        return (new MailMessage)
            ->subject('بيانات حساب الموظف')
            ->line('تم إنشاء حسابك بنجاح.')
            ->line('البريد الإلكتروني: ' . $notifiable->email)
            ->line('كلمة المرور المؤقتة: ' . $this->password)
            ->action('الانتقال إلى لوحة التحكم', $dashboardUrl)
            ->line('يرجى تغيير كلمة المرور عند أول تسجيل دخول.');
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
