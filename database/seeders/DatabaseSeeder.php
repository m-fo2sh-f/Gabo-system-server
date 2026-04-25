<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a default admin user
        User::create([
            'name' => 'gabo',
            'email' => 'client@gabo.com',
            'password' => Hash::make('12345678'), // الباسورد لازم يتشفر
        ]);
    }
}
