<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
}
