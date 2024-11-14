<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deck>
 */
class DeckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' =>$this->faker->name(),
            'nb_parties_gagnees' =>$this->faker->numberBetween(1,10),
            'cartes' =>$this->faker->words(15, true),
            'nb_parties_perdues' =>$this->faker->numberBetween(1,10),
            'prix' =>$this->faker->numberBetween(1,200),
            'pourcentage_utilisation' =>$this->faker->numberBetween(1,100),
            'utilisateur_id' =>$this->faker->numberBetween(1,10),
            'pourcentage_cartes_bleues' =>$this->faker->numberBetween(1,100),
            'pourcentage_cartes_jaunes' =>$this->faker->numberBetween(1,100),
            'pourcentage_cartes_rouges' =>$this->faker->numberBetween(1,100),
            'pourcentage_cartes_noires' =>$this->faker->numberBetween(1,100),
            'pourcentage_cartes_vertes' =>$this->faker->numberBetween(1,100),
            'pourcentage_cartes_blanches' =>$this->faker->numberBetween(1,100),
        ];
    }
}
