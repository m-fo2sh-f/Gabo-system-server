<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TaskType;
use App\Enums\TaskStatus;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'task_type' => ['nullable', new Enum(TaskType::class)],
            'status' => ['nullable', new Enum(TaskStatus::class)],
            'price' => ['required', 'numeric'],
            'cost' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string']
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cost' => $this->cost ?? 0,
        ]);
    }
}
