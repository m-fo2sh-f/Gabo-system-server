<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_date' => $this->transaction_date,
            
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // العلاقات
            'client_name' => $this->whenLoaded('client', function () {
                return $this->client->name;
            }),
            'employee_name' => $this->whenLoaded('employee', function () {
                return $this->employee->name;
            }),
            'task_name' => $this->whenLoaded('task', function () {
                return $this->task->name;
            }),
            'client' => new ClientResource($this->whenLoaded('client')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'task' => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
