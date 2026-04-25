<?php

namespace App\Services;
use App\Models\TaskType;


class TaskTypeService
{
    public function getAllTaskTypes()
    {
        return TaskType::all();
    }
   
    public function createTaskType(array $data)
    {
        return TaskType::create($data);
    }
    public function deleteTaskType(int $id)
    {
        $taskType = TaskType::find($id);
        $taskType->delete();
        return $taskType;
    }
}