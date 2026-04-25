<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Client;
use Carbon\Carbon;


#[Signature('app:check-late-payments')]
#[Description('Command description')]
class CheckLatePayments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Client::where('next_payment_date', '<', now())
            ->where('status', 'active')->where('payment_cycle', '!=', 'one_time')
            ->chunk(100, function ($clients) {
                foreach ($clients as $client) {
                    $installmentAmount = $client->contract_value; 


                    $newLateAmount = $client->late_amount + $installmentAmount;


                    $newNextDate = $this->calculateNextPaymentDate($client->next_payment_date, $client->payment_cycle);
                    $client->update([
                        'is_late' => true,
                        'late_amount' => $newLateAmount,
                        'next_payment_date' => $newNextDate
                    ]);
                }
            });

        $this->info("Late payments checked and updated successfully.");
    }
    public function calculateNextPaymentDate($startDate, $cycle)
    {

    if (!$startDate) {
        return null;
    }

    $date = Carbon::parse($startDate);

    return match($cycle) {
        'weekly'  => $date->addWeek()->toDateString(),
        'monthly' => $date->addMonthNoOverflow()->toDateString(),
        'one_time'=> null,
        default   => null,
    };
}
}
