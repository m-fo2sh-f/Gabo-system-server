<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;


class TaskService
{
    public function getTasks($request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page') === 'all' ? 10000 : $request->input('per_page', 15);
        return Task::with(['taskType', 'client', 'employee'])
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('client_id'), function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->filled('employee_id'), function($q) use ($request) {
                $q->where('employee_id', $request->employee_id);
            })
            ->when($request->filled('task_type'), function($q) use ($request) {
                $q->where('task_type_id', $request->task_type);
            })
            ->when($request->filled('status'), function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest() 
            ->paginate($perPage);
    }
    
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }
    public function getTaskById($id): Task
    {
        return Task::findOrFail($id);
    }
    public function updateTask($id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }
    public function deleteTask($id): Task
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return $task;
    }
}
