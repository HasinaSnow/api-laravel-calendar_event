<?php

namespace Database\Factories;

use App\Models\Money;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Process\FakeProcessResult;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::all()->random()->id,
            'money_id' => Money::all()->random()->id,
            'event_id' => Money::all()->random()->id
        ];
    }
}
