<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Task;
class TaskController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly TaskService $taskService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $tasks = $this->taskService->getTasks($request);
        return $this->successResponse(TaskResource::collection($tasks) , 'Tasks retrieved successfully');
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());
        return $this->successResponse(new TaskResource($task), 'Task created successfully', 201);
    }
    public function show(Task $task): JsonResponse
    {
        return $this->successResponse(new TaskResource($task));
    }
    public function update(Request $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateTask($task->id, $request->all());
        return $this->successResponse(new TaskResource($task), 'Task updated successfully', 200);
    }
    public function destroy(Task $task): JsonResponse
    {
        $task = $this->taskService->deleteTask($task->id);
        return $this->successResponse(new TaskResource($task), 'Task deleted successfully', 200);
    }
    
}
