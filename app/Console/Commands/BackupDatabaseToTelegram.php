<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ifsnop\Mysqldump as IMysqldump;

class BackupDatabaseToTelegram extends Command
{
    protected $signature = 'app:backup-db';
    protected $description = 'Take a database backup and send it via Telegram';

    public function handle()
    {
        $this->info('Starting database backup...');

        // 1. تحديد اسم الملف ومساره
        $fileName = 'backup_' . now()->format('Y_m_d_H_i') . '.sql';
        $filePath = storage_path('app/' . $fileName);

        try {
            // 2. سحب بيانات الاتصال من ملف الـ .env
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $dbname = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // 3. إنشاء الباك أب باستخدام المكتبة
            $dump = new IMysqldump\Mysqldump("mysql:host={$host};port={$port};dbname={$dbname}", $username, $password);
            $dump->start($filePath);

            $this->info('Backup created locally. Sending to Telegram...');

            // 4. إرسال الملف لتليجرام
            $baseUrl = config('telegram.api_url', 'https://api.telegram.org');
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])->timeout(120)->connectTimeout(30)->attach(
                'document', 
                file_get_contents($filePath), 
                $fileName
            )->post("{$baseUrl}/bot{$telegramToken}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => "📦 نسخة احتياطية جديدة للنظام\n📅 التاريخ: " . now()->format('Y-m-d H:i:s'),
            ]);

            // 5. التأكد من نجاح الإرسال ثم مسح الملف من السيرفر عشان المساحة
            if ($response->successful()) {
                $this->info('Backup sent successfully!');
                unlink($filePath); // مسح الملف من السيرفر
            } else {
                Log::error('Telegram Backup Failed: ' . $response->body());
                $this->error('Failed to send backup to Telegram.');
            }

        } catch (\Exception $e) {
            Log::error('Backup Exception: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
            
            // لو حصل خطأ والملف كان اتعمل، امسحه
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}