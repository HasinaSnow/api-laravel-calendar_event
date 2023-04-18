<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipement>
 */
class EquipementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->numberBetween(1000, 20000),
            'infos' => $this->faker->paragraph(),

            'service_id' => Service::all()->random()->id,
            
            'created_by' =>User::all()->random()->id,
            'updated_by' => User::all()->random()->id,
        ];
    }
}
