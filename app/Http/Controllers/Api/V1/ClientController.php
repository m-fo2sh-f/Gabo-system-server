<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// requests imports
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;

// resources imports
use App\Http\Resources\ClientResource;

// models imports
use App\Models\Client;



class ClientController extends Controller
{
    public function index(Request $request)
{
    $query = Client::query();

    $query->when($request->filled('search'), function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%')
        ->orWhere('phone', 'like', '%' . $request->search . '%');
    });
    $query->when($request->filled('status'), function ($q) use ($request) {
        $q->where('status', $request->status);
    }); 
    $clients = $query->latest()->paginate(15);

    return response()->json([
        'message' => 'Clients fetched successfully',
        'data' => ClientResource::collection($clients),
        'meta' => [
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
            'total' => $clients->total(),
        ]
    ], 200);
}

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $client = Client::create($data);
        return response()->json([
            'message' => 'Client created successfully',
            'data' => ClientResource::make($client)
        ],201);
    }

    public function show(Client $client)
    {
        return response()->json([
            'message' => 'Client found successfully',
            'data' => ClientResource::make($client)
        ], 200);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $data = $request->validated();
        return response()->json([
            'message' => 'Client updated successfully',
            'data' => ClientResource::make($client) 
        ], 200);
        
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json([
            'message' => 'Client deleted successfully',
        ], 200);
    }
}
