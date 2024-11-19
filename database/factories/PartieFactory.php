<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory qui créée des parties
 *
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
            'date' => $this->faker->date(),
            'nb_participants' => $this->faker->numberBetween(1, 8),
            'termine' => $this->faker->boolean(),
            'createur_id' => Utilisateur::factory(),
        ];
    }
}
