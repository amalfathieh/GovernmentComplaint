<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;

    public function __construct()
    {
        // جلب التوكن من الكونفيج لضمان الأمان
        $this->token = config('services.telegram.token');
    }

    /**
     * إرسال رسالة نصية
     */
    public function sendMessage()
    {
        try {
            $response = Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id' => 1069758461,
                'text' => "Hi Amal I love you",
                'parse_mode' => 'Markdown', // لتنسيق النص (bold, italic)
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال ملف (للتقارير)
     */
    public function sendDocument($chatId, $filePath, $caption = '')
    {
        try {
            $response = Http::attach(
                'document', file_get_contents($filePath), basename($filePath)
            )->post("https://api.telegram.org/bot{$this->token}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram File Error: " . $e->getMessage());
            return false;
        }
    }
}

