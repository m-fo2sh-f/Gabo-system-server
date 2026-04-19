<?php

namespace App\Http\Requests\Api\CLient;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',

            'brand_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'contract_start_date' => 'nullable|date',
            'contract_value' => 'nullable|numeric',
            'payment_cycle' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'next_payment_date' => 'nullable|date',
            'social_links' => 'nullable|array',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'phone.required' => 'The phone field is required.',
            'brand_name.required' => 'The brand name field is required.',
            'address.required' => 'The address field is required.',
            'contract_start_date.required' => 'The contract start date field is required.',
            'contract_value.required' => 'The contract value field is required.',
            'payment_cycle.required' => 'The payment cycle field is required.',
            'status.required' => 'The status field is required.',
            'next_payment_date.required' => 'The next payment date field is required.',
            'social_links.required' => 'The social links field is required.',
            'notes.required' => 'The notes field is required.',
        ];
    }
}
