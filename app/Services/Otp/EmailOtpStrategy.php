<?php


namespace App\Services\Otp;



use App\Notifications\EmailCodeNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class EmailOtpStrategy implements OtpStrategy
{
    public function send(string $receiver, string $code): bool
    {

        try {
        Notification::route('mail', $receiver)
            ->notify(new EmailCodeNotification($code));
            return true;

        } catch (\Exception $e) {
            \Log::error("Email OTP failed: " . $e->getMessage());
            return false;
        }
    }
}


