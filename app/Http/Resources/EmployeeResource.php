<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'job_title_id' => $this->job_title_id,
            'job_title' => $this->jobTitle->name ?? null,
            'employment_type' => $this->employment_type,
            'base_salary' => $this->base_salary !== null ? (float) $this->base_salary : null,
            'is_freelance' => (bool) $this->is_freelance,
            'commission_rate' => $this->commission_rate !== null ? (float) $this->commission_rate : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // العلاقات
        ];
    }
}
