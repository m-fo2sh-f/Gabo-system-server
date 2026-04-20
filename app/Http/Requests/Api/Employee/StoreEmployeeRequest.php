<?php

namespace App\Http\Requests\Api\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\EmployeeStatus;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_freelance' => ['required', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'commission_rate' => ['required_if:is_freelance,true', 'numeric', 'nullable'],
            'base_salary' => ['required_if:is_freelance,false', 'numeric', 'nullable'],
            'employee_status' => ['nullable', new Enum(EmployeeStatus::class)],
            'notes' => ['nullable', 'string']
        ];
    }
}
