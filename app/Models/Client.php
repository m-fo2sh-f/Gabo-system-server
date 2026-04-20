<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'brand_name',
        'address',
        'contract_start_date',
        'contract_value',
        'payment_cycle',
        'status',
        'next_payment_date',
        'social_links',
        'notes',
    ];

    protected $casts = [
        'social_links' => 'array',
        'contract_start_date' => 'date',
        'next_payment_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
