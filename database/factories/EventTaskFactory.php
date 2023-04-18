<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventTask>
 */
class EventTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::all()->random()->id,
            'task_id' => Task::all()->random()->id,

            'attribute_to' => User::all()->random()->id,
            'check' => fake()->boolean(),
            'expiration' => fake()->date(),
            
            'created_by' => User::all()->random()->id,
            'updated_by' => User::all()->random()->id,
        ];
    }
}
