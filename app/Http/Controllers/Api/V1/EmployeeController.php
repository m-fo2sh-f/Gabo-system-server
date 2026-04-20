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
class EmployeeController extends Controller
{
    use ApiResponseTrait;
    public function __construct(private readonly EmployeeService $employeeService) {}


    public function index(Request $request) : JsonResponse
    {
        $employees = $this->employeeService->getEmployees($request);
        return $this->successResponse(EmployeeResource::collection($employees), 'Employees fetched successfully');
    }


    public function store(StoreEmployeeRequest $request) : JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->validated());
        return $this->successResponse(new EmployeeResource($employee), 'Employee created successfully', 201);
    }
    public function show(Employee $employee) : JsonResponse
    {
        return $this->successResponse(new EmployeeResource($employee), 'Employee found successfully');
    }
    public function update(UpdateEmployeeRequest $request, Employee $employee) : JsonResponse
    {
        $employee = $this->employeeService->updateEmployee($employee->id, $request->validated());
        return $this->successResponse(new EmployeeResource($employee), 'Employee updated successfully');
    }
    public function destroy(Employee $employee) : JsonResponse
    {
        $employee = $this->employeeService->deleteEmployee($employee->id);
        return $this->successResponse(new EmployeeResource($employee), 'Employee deleted successfully');
    }
}
