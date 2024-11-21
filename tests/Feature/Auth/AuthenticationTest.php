<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $utilisateur = Utilisateur::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'courriel' => $utilisateur->courriel,
            'password' => 'password',
        ]);

        $token = $response->json('token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/utilisateurs');

        $this->assertAuthenticated();
        $response->assertStatus(200);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $this->post('/login', [
            'courriel' => $utilisateur->courriel,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->actingAs($utilisateur)->post('/logout');

        $this->assertGuest();
        $response->assertNoContent();
    }

}

