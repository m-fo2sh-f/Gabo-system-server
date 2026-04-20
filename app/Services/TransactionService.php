<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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
        return Transaction::create($data);
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function getTransactionById(int $id): Transaction
    {
        return Transaction::with(['client', 'employee', 'task'])->findOrFail($id);
    }
    

    public function deleteTransaction(int $id): void
    {
        Transaction::findOrFail($id)->delete();
    }
}
