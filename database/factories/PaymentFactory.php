<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paid' => fake()->boolean(20),
            'paid_at' => fake()->date(),

            'budget_id' =>Budget::all()->random()->id,
            
            'created_by' =>User::all()->random()->id,
            'updated_by' =>User::all()->random()->id,
        ];
    }
}
