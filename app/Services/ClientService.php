<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ClientService
{
    public function getAllClients(Request $request): LengthAwarePaginator
    {
        return Client::query()
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(15);
    }

    public function getClientById(int $id): Client
    {
        return Client::with('transactions')->findOrFail($id);
    }

    public function createClient(array $data): Client
{
    // هنا بنحسب ميعاد أول دفعة بناءً على تاريخ التعاقد اللي إنت باعتة
    if (empty($data['next_payment_date'])) {
            $data['next_payment_date'] = $this->calculateNextPaymentDate(
                $data['contract_start_date'] ?? null, 
                $data['payment_cycle'] ?? 'monthly'
            );
        }

        // 2. هندلة المديونية السابقة (للعملاء القدام):
        // لو اليوزر مبعتش قيم، بنعتبره عميل منتظم ومعليهوش فلوس
        $data['is_late'] = $data['is_late'] ?? false;
        $data['late_amount'] = $data['late_amount'] ?? 0.00;

        return Client::create($data);
}



    public function updateClient(int $id, array $data): Client
    {
        $client = $this->getClientById($id);
        $client->update($data);
        return $client;
    }

    public function deleteClient(int $id): void
    {
        $this->getClientById($id)->delete();
    }
    public function calculateNextPaymentDate($startDate, $cycle)
{
    if (!$startDate) return null;

    $date = Carbon::parse($startDate);

    return match($cycle) {
        'weekly'  => $date->addWeek()->toDateString(),
        'monthly' => $date->addMonthNoOverflow()->toDateString(),
        'one_time'=> null,
        default   => null,
    };
}
}
