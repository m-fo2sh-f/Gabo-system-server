<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
    use Carbon\Carbon;
use App\Models\Client;

class TransactionService
{
    public function getAllTransactions(Request $request): LengthAwarePaginator
    {
        return Transaction::with(['client', 'employee', 'task'])
        ->when($request->filled('type'), function($q) use ($request) {
            $q->where('type', $request->type);
        })
        ->when($request->filled('category'), function($q) use ($request) {
            $q->where('category', $request->category);
        })->latest()->paginate(15);
        
    }

    public function createTransaction(array $data): Transaction
{

  $transaction = Transaction::create($data);
if ($transaction->type !== 'income' || empty($data['client_id'])) {
    return $transaction;
}

$client = Client::findOrFail($data['client_id']);
$paidAmount = $data['amount'];

if ($client->is_late) {
    
    $newLateAmount = max(0, $client->late_amount - $paidAmount);
    
    $client->update([
        'late_amount' => $newLateAmount,
        'is_late' => $newLateAmount > 0, 
    ]);
} else {

    $client->update([
        'next_payment_date' => $this->calculateNextPaymentDate($client->next_payment_date, $client->payment_cycle)
    ]);
}

return $transaction;
    
}

    public function updateTransaction(int $id, array $data): Transaction
    {
        $data['next_payment_date'] = $this->calculateNextPaymentDate($data['start_date'] ?? null, $data['cycle']);
        $transaction = Transaction::findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function getTransactionById(int $id): Transaction
    {
        return Transaction::with(['client', 'employee', 'task'])->findOrFail($id);
    }
    

   public function deleteTransaction(int $id): Transaction
    {
        $transaction = Transaction::findOrFail($id); // صلحنا الـ Typo
        $transaction->delete();
        
        return $transaction;
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

