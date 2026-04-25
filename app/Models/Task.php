<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function taskType() { return $this->belongsTo(TaskType::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
