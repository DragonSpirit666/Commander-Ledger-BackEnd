<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Utilisateur::factory(10)->create();

        Utilisateur::factory()->create([
            'name' => 'Test Utilisateur',
            'email' => 'test@example.com',
        ]);
    }
}
