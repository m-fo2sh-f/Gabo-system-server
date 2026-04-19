<?php

namespace Database\Factories;

use App\Models\JobTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'job_title_id' => JobTitle::factory(),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'internship']),
            'base_salary' => fake()->randomFloat(2, 500, 5000),
            'is_freelance' => fake()->boolean(),
            'commission_rate' => fake()->randomFloat(2, 0, 20),
            'status' => fake()->randomElement(['active', 'paused', 'stopped']),
            'notes' => fake()->sentence(),
        ];
    }
}
