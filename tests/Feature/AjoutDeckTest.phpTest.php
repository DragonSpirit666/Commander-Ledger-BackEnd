<?php
namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe("Test effectué sur l'ajout de deck", function() {
    test("La création de deck", function() {
        $user = Utilisateur::factory()->create();

        $payload = [
            'nom' => 'Test Deck',
            'cartes' => "4 Lightning Bolt\n2 Giant Growth\n1 Island",
        ];

        $response = $this->postJson(
            url("http://0.0.0.0:80/commander-ledger/utilisateurs/{$user->id}/decks"),
            $payload
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('decks', [
            'nom' => 'Test Deck',
            'utilisateur_id' => $user->id,
        ]);

        $responseData = $response->json();

        expect($responseData[0]['nom'])->toBe('Test Deck')
            ->and($responseData[0])->toHaveKeys([
                'prix',
                'pourcentage_cartes_blanches',
                'pourcentage_cartes_rouges',
            ]);
    });


});
