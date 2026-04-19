<?php

namespace App\Http\Requests\Api\CLient;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255| unique:clients,name',
            'phone' => 'required|string|max:255',

            'brand_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'contract_start_date' => 'nullable|date',
            'contract_value' => 'nullable|numeric',
            'payment_cycle' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'next_payment_date' => 'nullable|date',
            'social_links' => 'nullable|array|max:255',
            'social_links.*.platform' => 'nullable|string|in:facebook,instagram,linkedin,snapchat,tiktok,website',
            'social_links.*.url' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'name is required',
            'phone.required' => 'phone is required',
            'brand_name.required' => 'brand name is required',
            'address.required' => 'address is required',
            'contract_start_date.required' => 'contract start date is required',
            'contract_value.required' => 'contract value is required',
            'payment_cycle.required' => 'payment cycle is required',
            'status.required' => 'status is required',
            'next_payment_date.required' => 'next payment date is required',
            'social_links.required' => 'social links is required',
            'notes.required' => 'notes is required',
        ];
    }
}
