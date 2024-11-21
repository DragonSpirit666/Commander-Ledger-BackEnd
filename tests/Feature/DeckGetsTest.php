<?php
use App\Models\Deck;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::Class);

describe('DeckGetsTest', function () {
    test('Peut récuperer tous les decks d\'un utilisateur' , function () {
        $utilisateur = Utilisateur::factory()->create();
        $deck = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);
        $deck2 = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur->id}/decks");

        dd($response->json());

        $response->assertStatus(200);
        $response->assertJsonFragment(['nom' => $deck->nom]);
        $response->assertJsonFragment(['nom' => $deck2->nom]);
    });

    test('Peut récuperer un deck d\'un utilisateur' , function () {
        $utilisateur = Utilisateur::factory()->create();
        $deck = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur->id}/decks/{$deck->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['nom' => $deck->nom]);
    });

    test('Donne une erreur si le deck n\'existe pas' , function () {
        $utilisateur = Utilisateur::factory()->create();

        $this->actingAs($utilisateur);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur->id}/decks/1");

        $response->assertStatus(404);
    });

    test('Donne une erreur si la requête est mal formée' , function () {
        $utilisateur = Utilisateur::factory()->create();

        $this->actingAs($utilisateur);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur->id}/decks/abc");

        $response->assertStatus(400);
    });
});
