<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour ami
 */
class AmiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'utilisateur_demandeur_id' => Utilisateur::factory(),
            'utilisateur_receveur_id' => Utilisateur::factory(),
            'invitation_accepter' => $this->faker->boolean(50),
        ];
    }
}
