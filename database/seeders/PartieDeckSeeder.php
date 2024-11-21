<?php

namespace Database\Seeders;

use App\Models\PartieDeck;
use Illuminate\Database\Seeder;

/**
 * Seeder la table parties_decks
 */
class PartieDeckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartieDeck::factory()->count(10)->create();
    }
}
