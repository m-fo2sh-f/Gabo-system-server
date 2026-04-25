<?php

namespace App\Http\Requests\Api\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TransactionType;
use App\Enums\TransactionCategory;
use App\Enums\PaymentMethod;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(TransactionType::class)],
            'category' => ['required', new Enum(TransactionCategory::class)],
            'amount' => ['required', 'numeric'],
            'payment_method' => ['nullable', new Enum(PaymentMethod::class)],
            'transaction_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'collector_id' => ['nullable', 'integer', 'exists:users,id'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('payment_method')) {
            $this->merge(['payment_method' => PaymentMethod::CASH->value]);
        }
    }
}
