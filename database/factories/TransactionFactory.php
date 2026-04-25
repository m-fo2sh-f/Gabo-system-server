<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['income', 'expense']),
            'category' => fake()->randomElement(['task_payment', 'manual_collection', 'salary', 'ads', 'software', 'other']),
            'amount' => fake()->randomFloat(2, 10, 2000),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'credit_card', 'vodafone_cash']),
            'transaction_date' => fake()->date(),
            // Ensure related entities are cleanly mocked
            'client_id' => fake()->boolean(60) ? Client::factory() : null,
            'employee_id' => fake()->boolean(20) ? Employee::factory() : null,
            'task_id' => fake()->boolean(50) ? Task::factory() : null,
            'user_id' => User::factory(),
            'notes' => fake()->sentence(),
        ];
    }
}
