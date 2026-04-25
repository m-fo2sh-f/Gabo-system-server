<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Employee;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'client_id' => Client::factory(),
           
            'employee_id' => Employee::factory(),
            'task_type_id' => TaskType::factory(),
            'price' => fake()->randomFloat(2, 1000, 5000),
            'cost' => fake()->randomFloat(2, 100, 800),
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'notes' => fake()->sentence(),
        ];
    }
}
