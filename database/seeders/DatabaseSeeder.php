<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([UtilisateurSeeder::class, PartieSeeder::class, DeckSeeder::class, PartieDeckSeeder::class, AmiSeeder::class]);
    }
}
