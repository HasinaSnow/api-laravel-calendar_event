<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventService>
 */
class EventServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::all()->random()->id,
            'event_id' => Event::all()->random()->id,
            
            'created_by' => User::all()->random()->id,
            'updated_by' => User::all()->random()->id,
        ];
    }
}
