<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            "cost"=> $this->cost,
            "price"=> $this->price,
            
            "start_date"=> $this->start_date,
            "end_date"=> $this->end_date,
            "notes"=> $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'task_type' => $this->taskType->name ?? null, 
            'client' => $this->client->name ?? null,
            'employee' => $this->employee->name ?? null,
            'task_type_id'     => $this->taskType->id ?? null, 
            "task_type_name"=> $this->taskType->name,
            "client_name"=> $this->client->name,
            "employee_name"=> $this->employee->name,
            'client_id'   => $this->client->id ?? null,
            'employee_id' => $this->employee->id ?? null,
        ];
    }
}
