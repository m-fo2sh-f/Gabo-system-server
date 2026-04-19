<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'brand_name' => $this->brand_name,
            'address' => $this->address,
            'contract_start_date' => $this->contract_start_date,
            'contract_value' => $this->contract_value,
            'payment_cycle' => $this->payment_cycle,
            'status' => $this->status,
            'next_payment_date' => $this->next_payment_date,
            'social_links' => $this->social_links,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
