<?php

namespace App\Http\Requests\Api\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\EmployeeStatus;
use Illuminate\Validation\Rules\Enum;


class UpdateEmployeeRequest extends FormRequest
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
            'employment_type' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['nullable', 'numeric', 'nullable'],
            'commission_rate' => ['nullable', 'numeric', 'nullable'],
            'job_title_id' => ['nullable', 'integer', 'nullable'],
            'status' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'commission_rate' => ['nullable', 'numeric', 'nullable'],
            'base_salary' => ['nullable', 'numeric', 'nullable'],
            'employee_status' => ['nullable', new Enum(EmployeeStatus::class)],
            'notes' => ['nullable', 'string']
        ];
    }
}
