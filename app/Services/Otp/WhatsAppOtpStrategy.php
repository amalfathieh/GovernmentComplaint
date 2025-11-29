<?php


namespace App\Services\Otp;


use Illuminate\Support\Facades\Http;

class WhatsAppOtpStrategy implements OtpStrategy
{
    private string $instanceId;
    private string $token;

    public function __construct()
    {

        $this->instanceId = config('app.green_instance_id');
        $this->token = config('app.green_api_token');
    }

    public function send(string $receiver, string $code): bool
    {
        try {
            $chatId = "{$receiver}@c.us";
            $url = "https://7107.api.green-api.com/waInstance{$this->instanceId}/sendMessage/{$this->token}";
            $response = Http::post($url, [
                'chatId' => $chatId,
                'message' => "رمز التحقق الخاص بك هو: {$code}
                .صالح لمدة 30 دقيقة من وقت الارسال"
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error("WhatsApp OTP failed: " . $e->getMessage());
            return false;
        }
    }
}
