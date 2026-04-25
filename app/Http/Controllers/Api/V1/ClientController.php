<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// services imports
use App\Services\ClientService;

// traits imports
use App\Traits\ApiResponseTrait;

// requests imports
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;

// resources imports
use App\Http\Resources\ClientResource;

// models imports
use App\Models\Client;




class ClientController extends Controller

{
    use ApiResponseTrait;


    public function __construct(private readonly ClientService $clientService){}

    public function index(Request $request) : JsonResponse
    {
        $clients =$this->clientService->getAllClients($request);
        return $this->successResponse([
            'data' => ClientResource::collection($clients),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'length' => $clients->count(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
            ],
        ], 'Clients retrieved successfully');
    }

    public function store(Request $request) : JsonResponse
    {
        $client = $this->clientService->createClient($request->all());
        return $this->successResponse(new ClientResource($client), 'Client created successfully', 201);
    }

    public function show(int $id) : JsonResponse
    {
       $data = $this->clientService->getClientById($id);
    
        $client = $data['client'];
        $transactions = $data['transactions']; 
        $responseData = [
            'client' => $client,
            'transactions' => $transactions->items(), 
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
            'total' => $transactions->total(),
        ]
    ];
        return $this->successResponse($responseData, 'Client details retrieved successfully');
    }

    public function update(Request $request, Client $client) : JsonResponse
    {
        $client = $this->clientService->updateClient($client->id, $request->all());
        return $this->successResponse(new ClientResource($client), 'Client updated successfully');
        
    }

    public function destroy(Client $client) : JsonResponse
    {
        $client->delete();
        return $this->successResponse(new ClientResource($client), 'Client deleted successfully');
    }
}
