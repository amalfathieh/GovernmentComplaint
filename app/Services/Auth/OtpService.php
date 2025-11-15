<?php


namespace App\Services\Auth;

use App\Models\Otp;
use App\Services\Otp\{
//    OtpContext,
    EmailOtpStrategy,
    WhatsAppOtpStrategy
};
use Carbon\Carbon;

class OtpService
{
    public function sendOtp(string $receiver, string $type): bool
    {
        $code = rand(1000, 9999);

        // حفظ الكود في قاعدة البيانات
        Otp::create([
            'receiver' => $receiver,
            'code' => $code,
        ]);

        // اختيار طريقة الإرسال
        $strategy = $type === 'email'
            ? new EmailOtpStrategy()
            : new WhatsAppOtpStrategy();

        return $strategy->send($receiver, $code);
    }
}
