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
    'password' => Hash::make('password123'), // الباسورد لازم يتشفر
]);

        // 2. Seed Lookups / Parent Tables (10 records each)
        $jobTitles = JobTitle::factory(5)->create();
        $taskTypes = TaskType::factory(5)->create();

        // 3. Seed Main Entities (50 records each)
        // using recycle avoids generating new nested models
        $clients = Client::factory(50)->create();
        
        $employees = Employee::factory(50)
            ->recycle($jobTitles)
            ->create();

        // 4. Seed Child Tables (Tasks -> relies on Client, Employee, TaskType)
        $tasks = Task::factory(100)
            ->recycle([$clients, $employees, $taskTypes])
            ->create();

        // 5. Seed Transactions (Depends on everything)
        // 200 transactions scattered across the fake objects
        Transaction::factory(200)
            ->recycle([
                $clients, 
                $employees, 
                $tasks, 
                User::all()
            ])
            ->create();
    }
}
