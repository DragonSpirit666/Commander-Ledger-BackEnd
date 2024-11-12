<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partie>
 */
class PartieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'termine' => true,
            'createur_id' => Utilisateur::factory()->create()->id
        ];
    }
}
