<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // TEST À CORRIGER

    /*public function test_users_can_authenticate_using_the_login_screen(): void
    {
        // Création d'un utilisateur via la factory avec un mot de passe connu
        $utilisateur = Utilisateur::factory()->create([
            'password' => bcrypt('password'), // Assurez-vous que le mot de passe est encrypté
        ]);

        // Simulation d'une requête POST pour se connecter
        $response = $this->postJson('/login', [
            'courriel' => $utilisateur->courriel,
            'password' => 'password', // Utilise le mot de passe défini dans la factory
        ]);

        // Vérifie que la réponse contient un token
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());

        // Récupère le token pour l'utiliser dans la prochaine requête
        $token = $response->json('token');

        // Utilisation du token pour une requête authentifiée
        $authenticatedResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/utilisateurs');

        // Vérifie que l'utilisateur est bien authentifié
        $this->assertAuthenticated();

        // Vérifie que la réponse de l'API renvoie un statut HTTP 200
        $authenticatedResponse->assertStatus(200);

        // Vérifie que les données retournées sont conformes
        $authenticatedResponse->assertJsonStructure([
            '*' => ['id', 'nom', 'courriel', 'photo', 'prive'], // Assurez-vous que ces champs correspondent à votre structure
        ]);
    }*/

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $this->post('/login', [
            'courriel' => $utilisateur->courriel,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /*public function test_users_can_logout(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->actingAs($utilisateur)->post('/logout');

        $this->assertGuest();
        $response->assertNoContent();
    }*/

}

