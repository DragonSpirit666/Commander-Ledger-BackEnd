<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property string $password
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    /**
     * Defini l'état par défaut du modele.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => fake()->name(),
            'courriel' => fake()->unique()->safeEmail(),
            'photo' => fake()->imageUrl(),
            'prive' => fake()->boolean,
            'password' => bcrypt('password'),
        ];
    }
}
