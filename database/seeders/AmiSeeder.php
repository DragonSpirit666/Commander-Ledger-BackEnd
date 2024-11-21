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
        $users = Utilisateur::factory(10)->create();

        foreach ($users as $user1) {
            foreach ($users as $user2) {
                if ($user1->id !== $user2->id) {
                    Ami::factory()->create([
                        'user_1_id' => $user1->id,
                        'user_2_id' => $user2->id,
                    ]);
                }
            }
        }
    }
}
