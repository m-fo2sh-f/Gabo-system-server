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
            'task_type'     => $this->taskType->name ?? null, 
            'client_name'   => $this->client->name ?? null,
            'employee_name' => $this->employee->name ?? null,
            "start_date"=> $this->start_date,
            "end_date"=> $this->end_date,
            "notes"=> $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
