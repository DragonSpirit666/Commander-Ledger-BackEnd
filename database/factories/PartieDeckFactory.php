<?php

namespace Database\Factories;

use App\Models\Deck;
use App\Models\Partie;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartieDeck>
 */
class PartieDeckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partie_id' => Partie::factory(),
            'deck_id' => Deck::factory(),
        ];
    }
}
