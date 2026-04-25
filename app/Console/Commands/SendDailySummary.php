<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\Transaction;
use App\Models\Client;
use Carbon\Carbon;

class SendDailySummary extends Command
{
    // اسم الأمر اللي هننادي عليه بيه
    protected $signature = 'app:send-daily-summary';

    // وصف الأمر
    protected $description = 'Send a daily summary of system data (Income, Expenses, and Client Payments) to Telegram';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Gathering daily data...');

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // 1. تجميع الداتا المالية لليوم الحالي فقط
        $dailyIncome = Transaction::where('type', 'income')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $dailyExpense = Transaction::where('type', 'expense')
            ->whereDate('transaction_date', $today)
            ->sum('amount');
            
        $netProfit = $dailyIncome - $dailyExpense;

        // 2. تجميع العملاء اللي معاد دفعهم النهارده أو بكرة (النشطين فقط)
        $upcomingPayments = Client::where('status', 'active')
            ->whereIn('next_payment_date', [$today->toDateString(), $tomorrow->toDateString()])
            ->get();

        // 3. تجميع العملاء المتأخرين واللي عليهم فلوس
        $lateClients = Client::where('status', 'active')
            ->where('is_late', true)
            ->where('late_amount', '>', 0)
            ->get();

        // 4. بناء هيكل الرسالة باستخدام HTML Tags المدعومة في تليجرام
        $message = "📊 <b>التقرير اليومي لنظام (Agora System)</b>\n";
        $message .= "📅 التاريخ: " . $today->format('Y-m-d') . "\n\n";

        // -- القسم المالي --
        $message .= "💰 <b>الملخص المالي لليوم:</b>\n";
        $message .= "🟢 الإيرادات: <b>" . number_format($dailyIncome, 2) . " ج.م</b>\n";
        $message .= "🔴 المصروفات: <b>" . number_format($dailyExpense, 2) . " ج.م</b>\n";
        
        // إيموجي الربح أو الخسارة
        $profitEmoji = $netProfit >= 0 ? "📈" : "📉";
        $message .= "{$profitEmoji} الصافي: <b>" . number_format($netProfit, 2) . " ج.م</b>\n\n";

        // -- قسم مواعيد الدفع --
        $message .= "🗓️ <b>مواعيد الدفع (اليوم وغداً):</b>\n";
        if ($upcomingPayments->isEmpty()) {
            $message .= "➖ لا يوجد عملاء موعد دفعهم اليوم أو غداً.\n";
        } else {
            foreach ($upcomingPayments as $client) {
                // تحديد الكلمة (اليوم) ولا (غداً)
                $dateLabel = $client->next_payment_date == $today->toDateString() ? '(اليوم)' : '(غداً)';
                $message .= "👤 {$client->name} {$dateLabel} \n";
            }
        }
        $message .= "\n";

        // -- قسم المتأخرات --
        $message .= "⚠️ <b>العملاء المتأخرين:</b>\n";
        if ($lateClients->isEmpty()) {
            $message .= "✅ لا يوجد عملاء متأخرين، السيستم زي الفل!\n";
        } else {
            foreach ($lateClients as $client) {
                $message .= "🚨 {$client->name} - مديونية: <b>" . number_format($client->late_amount, 2) . " ج.م</b>\n";
            }
        }
        // 5. إرسال الرسالة
        $isSent = $telegramService->sendMessage($message);

        if ($isSent) {
            $this->info('Summary sent successfully via Telegram!');
        } else {
            $this->error('Failed to send summary. Check your logs.');
        }
    }
}