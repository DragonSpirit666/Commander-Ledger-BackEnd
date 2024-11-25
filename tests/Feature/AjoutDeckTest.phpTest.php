<?php
namespace Tests\Feature;

use App\Models\Deck;
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

        $deck = Deck::where('nom', 'Test Deck')->where('utilisateur_id', $user->id)->first();

        expect($deck)->not->toBeNull()
            ->and($deck->nom)->toBe('Test Deck')
            ->and($deck->prix)->not->toBeNull()
            ->and($deck->pourcentage_cartes_blanches)->not->toBeNull()
            ->and($deck->pourcentage_cartes_rouges)->not->toBeNull()
            ->and($responseData[0]['nom'])->toBe('Test Deck')
            ->and($responseData[0])->toHaveKeys([
                'prix',
                'pourcentage_cartes_blanches',
                'pourcentage_cartes_rouges',
            ]);

    });

    test("La création de deck avec des erreurs (format incorrect)", function() {
        $user = Utilisateur::factory()->create();

        // Payload avec des données incorrectes (cartes en mauvais format)
        $payload = [
            'nom' => 'Deck Incorrect',
            'cartes' => "Lightning Bolt sans quantité spécifiée", // Format incorrect
        ];

        $response = $this->postJson(
            url("http://0.0.0.0:80/commander-ledger/utilisateurs/{$user->id}/decks"),
            $payload
        );

        // Assurer que la réponse est une erreur de validation
        $response->assertStatus(422);

        // Vérifier qu'aucun deck n'a été ajouté à la base de données
        $this->assertDatabaseMissing('decks', [
            'nom' => 'Deck Incorrect',
            'utilisateur_id' => $user->id,
        ]);
    });
});
