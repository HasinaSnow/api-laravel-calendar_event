<?php

namespace Database\Factories;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipementEvent>
 */
class EquipementEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(0, 50);
        $equipementId = fake()->numberBetween(1, 10);

        return [
            'equipement_id' => $equipementId,
            'event_id' => fake()->numberBetween(1, 3),
            'quantity' => $quantity, 
            'amount' => ($this->equipementPrice($equipementId) * $quantity),
            'created_by' => User::all()->random()->id,
            'updated_by' => User::all()->random()->id,
        ];
    }
    
    /**
     * get phe price of equipement
     * @param int $equipmentId
     *
     * @return int
     */
    private function equipementPrice(int $equipementId)
    {
        return DB::selectOne(
            'SELECT price 
                FROM equipements 
                WHERE id = ?', 
            [$equipementId])->price;
    }

}
