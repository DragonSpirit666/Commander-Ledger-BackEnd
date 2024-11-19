<?php

namespace Database\Factories;

use App\Models\Deck;
use App\Models\Partie;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * Factory qui créée des parties_decks
 *
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
