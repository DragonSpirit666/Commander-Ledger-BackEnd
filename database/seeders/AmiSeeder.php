<?php

namespace Database\Seeders;

use App\Models\Ami;
use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmiSeeder extends Seeder
{
    /**
     * Run the database seed for Ami.
     *
     * @return void
     */
    public function run(): void
    {
        $users = Utilisateur::all();

        Ami::factory()
            ->count(10)
            ->create()
            ->each(function ($ami) use ($users) {
                $ami->utilisateur_demandeur_id = $users->random()->id;
                $ami->utilisateur_receveur_id = $users->random()->id;

                while ($ami->utilisateur_demandeur_id === $ami->utilisateur_receveur_id) {
                    $ami->utilisateur_receveur_id = $users->random()->id;
                }

                $ami->save();
            });
    }
}
