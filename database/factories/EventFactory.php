<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Client;
use App\Models\Confirmation;
use App\Models\Pack;
use App\Models\Place;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'date' => fake()->dateTimeBetween('now', '+2 months'),
            'audience' => fake()->boolean(),

            'category_id' => Category::all()->random()->id,
            'place_id' => Place::all()->random()->id,
            'confirmation_id' => Confirmation::all()->random()->id,
            'type_id' => Type::all()->random()->id,
            'client_id' => Client::all()->random()->id,
            'pack_id' => Pack::all()->random()->id,
            
            'created_by' => User::all()->random()->id,
            'updated_by' => User::all()->random()->id,
        ];
    }
}
