<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;




class EmployeeService
{
    public function getEmployees($request): LengthAwarePaginator
    {
        return Employee::with('jobTitle')
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('type'), function($q) use ($request) {
                $type = $request->type;
                $mainTypes = ['full_time', 'part_time', 'internship'];
                if (in_array($type, $mainTypes)) {
                    $q->where('employment_type', $type);
                } 
                elseif ($type === 'freelance') {
                    $q->where('is_freelance', true);
                }
            })
            ->latest()
            ->paginate(15);
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
