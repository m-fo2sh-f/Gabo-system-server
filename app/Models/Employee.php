<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
        'employment_type',
        'job_title_id',
        'is_freelance',
        'commission_rate',
        'status',
        'notes',
    ];
    protected $casts = [
        'job_title_id' => 'integer',
        'is_freelance' => 'boolean',
    ];
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_employees', 'employee_id', 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
