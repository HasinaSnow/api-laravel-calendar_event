<?php

namespace Database\Factories;

use App\Models\Deposit;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(1000, 20000),
            'infos' => fake()->paragraph(),
            
            'event_id' => Event::all()->random()->id,
            
            'created_by' => User::all()->random()->id,
        ];
    }
}
