<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\Api\Employee\StoreEmployeeRequest;
use App\Http\Requests\Api\Employee\UpdateEmployeeRequest;
use App\Traits\ApiResponseTrait;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;

class EmployeeController extends Controller
{
    use ApiResponseTrait;
    public function __construct(private readonly EmployeeService $employeeService) {}


    public function index(Request $request) : JsonResponse
    {
        $employees = $this->employeeService->getEmployees($request);
        return $this->successResponse([
            'data' => EmployeeResource::collection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'length' => $employees->count(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ], 'Employees fetched successfully');
    }


    public function store(Request $request) : JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->all());
        return $this->successResponse(new EmployeeResource($employee), 'Employee created successfully', 201);
    }

    public function show(int $id) : JsonResponse
    {
       $data = $this->employeeService->getEmployeeById($id);
    
        $employee = $data['employee'];
        $tasks = $data['tasks']; 
        $responseData = [
            'employee' => $employee,
            'tasks' => $tasks->items(), 
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ];
        return $this->successResponse($responseData, 'Employee details retrieved successfully');
    }
    public function update(Request $request, Employee $employee) : JsonResponse
    {
        $employee = $this->employeeService->updateEmployee($employee->id, $request->all());
        return $this->successResponse(new EmployeeResource($employee), 'Employee updated successfully');
    }
    public function destroy(Employee $employee) : JsonResponse
    {
        $employee = $this->employeeService->deleteEmployee($employee->id);
        return $this->successResponse(new EmployeeResource($employee), 'Employee deleted successfully');
    }
}
