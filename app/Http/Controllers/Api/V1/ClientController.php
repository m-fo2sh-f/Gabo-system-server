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
        return $this->successResponse(ClientResource::collection($clients), 'Clients fetched successfully');
    }

    public function store(StoreClientRequest $request) : JsonResponse
    {
        $client = $this->clientService->createClient($request->validated());
        return $this->successResponse(new ClientResource($client), 'Client created successfully', 201);
    }

    public function show(Client $client) : JsonResponse
    {
        return $this->successResponse(new ClientResource($client), 'Client found successfully');
    }

    public function update(UpdateClientRequest $request, Client $client) : JsonResponse
    {
        $data = $request->validated();
        return $this->successResponse(new ClientResource($client), 'Client updated successfully');
        
    }

    public function destroy(Client $client) : JsonResponse
    {
        $client->delete();
        return $this->successResponse(new ClientResource($client), 'Client deleted successfully');
    }
}
