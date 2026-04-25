<?php

namespace App\Http\Requests\Api\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ClientStatus;
use App\Enums\PaymentCycle;
use App\Enums\SocialPlatform;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'contract_start_date' => ['nullable', 'date'],
            'contract_value' => ['nullable', 'numeric'],
            'next_payment_date' => ['nullable', 'date'],
            'late_amount' => ['nullable', 'numeric'],
            'is_late' => ['nullable', 'boolean'],
            'payment_cycle' => ['nullable', new Enum(PaymentCycle::class)],
            'status' => ['nullable', new Enum(ClientStatus::class)],
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required_with:social_links', new Enum(SocialPlatform::class)],
            'social_links.*.url' => ['required_with:social_links', 'url'],
            'notes' => ['nullable', 'string']
        ];
    }
}
