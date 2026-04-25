<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $chatId;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}/sendMessage";
    }

    /**
     * إرسال رسالة إلى تليجرام
     */
    public function sendMessage(string $message): bool
    {
        try {
            $response = Http::withoutVerifying()->post($this->apiUrl, [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML', // عشان تقدر تنسق الرسالة بـ HTML
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            // تسجيل الخطأ لو حصلت مشكلة عشان متوقفش السيستم
            Log::error('Telegram Bot Error: ' . $e->getMessage());
            return false;
        }
    }
}