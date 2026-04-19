<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'employment_type',
        'job_title_id',
        'is_freelance',
        'commission_rate',
        'status',
        'notes',
    ];
}
