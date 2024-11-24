<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Utilisateur|Collection|Model $utilisateur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->utilisateur = Utilisateur::factory()->create();
        $this->token = $this->utilisateur->createToken('Test Token')->plainTextToken;
    }

    public function testPeutRecupererTousLesUtilisateurs()
    {
        $user = Utilisateur::factory()->create();

        $token = $user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/commander-ledger/utilisateurs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'nom',
                    'photo',
                    'prive',
                    'nb_parties_gagnees',
                    'nb_parties_perdues',
                    'prix_total_decks',
                ]
            ]
        ]);
    }

    public function testPeutMettreAJourUnUtilisateur()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/commander-ledger/utilisateurs/' . $this->utilisateur->id, [
            'nom' => 'Nouveau Nom',
            'courriel' => 'nouveau@exemple.com',
            'prive' => true
        ]);

        $response->assertStatus(200);
        $utilisateur = Utilisateur::find($this->utilisateur->id);
        assertEquals('Nouveau Nom', $utilisateur->nom);
        assertEquals('nouveau@exemple.com', $utilisateur->courriel);
        assertTrue($utilisateur->prive);
    }

    public function testPeutSupprimerUnUtilisateur()
    {
        // TODO fail parce qu'on peut pu get le user quand yer soft delete comme ca
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/commander-ledger/utilisateurs/' . $this->utilisateur->id);

        $response->assertStatus(200);

        $utilisateur = Utilisateur::find($this->utilisateur->id);
        assertEquals('Inconnu'.$this->utilisateur->id, $utilisateur->nom);
    }
}

