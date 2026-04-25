<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
// services imports
use App\Services\TaskTypeService;
// traits imports
use App\Traits\ApiResponseTrait;
// models imports
use App\Models\TaskType;


class TaskTypeController extends Controller
{
    
    use ApiResponseTrait;
    public function __construct(private readonly TaskTypeService $taskTypeService)
    {
    }
    public function index() : JsonResponse
    {
        $taskTypes = $this->taskTypeService->getAllTaskTypes();
        return $this->successResponse($taskTypes, 'Task types retrieved successfully');
        
    }

    public function store(Request $request)
    {
        $taskType = $this->taskTypeService->createTaskType($request->all());
        return $this->successResponse($taskType, 'Task type created successfully', 201);
        
    }

   

  

    public function destroy(int $id)
    {
        $taskType = $this->taskTypeService->deleteTaskType($id);
        return $this->successResponse($taskType, 'Task type deleted successfully');
    }
}
