<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly TransactionService $transactionService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->getAllTransactions($request);
        return $this->successResponse(TransactionResource::collection($transactions), 'Transactions fetched successfully');
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionService->getTransactionById($id);
        return $this->successResponse(new TransactionResource($transaction));
    }
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());
        return $this->successResponse(new TransactionResource($transaction), 'Transaction created successfully', 201);
    }
    public function update(Request $request, int $id): JsonResponse
    {
        $transaction = $this->transactionService->updateTransaction($id , $request->all());
        return $this->successResponse(new TransactionResource($transaction), 'Transaction updated successfully', 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->transactionService->deleteTransaction($id);
        return $this->successResponse(new TransactionResource($transaction), 'Transaction deleted successfully');
    }
}
