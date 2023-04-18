<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Remainder>
 */
class RemainderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expiration' => fake()->dateTimeBetween('now', '+2 months'),
            'rate' => fake()->numberBetween(0, 100),
            'infos'=> fake()->sentence(),

            'payment_id' => Payment::all()->random()->id,

            'created_by' =>User::all()->random()->id,
            'updated_by' =>User::all()->random()->id,
        ];
    }
}
