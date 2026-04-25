<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;




class EmployeeService
{
    public function getEmployees($request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page') === 'all' ? 10000 : $request->input('per_page', 15);
        return Employee::with('jobTitle')
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('employment_type'), function($q) use ($request) {
                $q->where('employment_type', $request->employment_type);
            })
            ->when($request->filled('job_title_id'), function($q) use ($request) {
                $q->where('job_title_id', $request->job_title_id);
            })
            ->latest()
            ->paginate($perPage);
    }
    public function getEmployeeById(int $id): array
    {
        $employee = Employee::with('jobTitle')->findOrFail($id);
        $tasks = $employee->tasks()->with(['taskType'])->latest()->paginate(15);
        return compact('employee', 'tasks');
    }
    public function createEmployee(array $data): Employee
    {
        return Employee::create($data);
    }
    public function updateEmployee(int $id, array $data): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }
    public function deleteEmployee(int $id): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return $employee;
    }
}
