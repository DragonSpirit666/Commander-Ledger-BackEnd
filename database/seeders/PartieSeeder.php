<?php

namespace Database\Seeders;

use App\Models\Partie;
use Illuminate\Database\Seeder;

/**
 * Seeder pour la table des parties
 */
class PartieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Partie::factory()->count(10)->create();
    }
}
