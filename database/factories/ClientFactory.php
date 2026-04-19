<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
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
            'brand_name' => fake()->company(),
            'address' => fake()->address(),
            'contract_start_date' => fake()->date(),
            'contract_value' => fake()->randomFloat(2, 1000, 10000),
            'payment_cycle' => fake()->randomElement(['one_time', 'weekly', 'monthly']),
            'status' => fake()->randomElement(['active', 'paused', 'stopped']),
            'next_payment_date' => fake()->date(),
            'social_links' => [
                ['platform' => 'facebook', 'url' => fake()->url()],
                ['platform' => 'instagram', 'url' => fake()->url()],
            ],
            'notes' => fake()->sentence(),
        ];
    }
}
