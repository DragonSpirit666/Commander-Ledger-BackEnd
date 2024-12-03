<?php
use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::Class);

describe('Test les routes pour get des decks', function () {
    test('Peut récuperer tous les decks d\'un utilisateur' , function () {
        $utilisateur = Utilisateur::factory()->create();
        $deck = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);
        $deck2 = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur->id}/decks");

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

    test('Calcule les parti gagnees et perdu' , function () {
        $utilisateur1 = Utilisateur::factory()->create();
        $utilisateur2 = Utilisateur::factory()->create();
        $utilisateur3 = Utilisateur::factory()->create();
        $deck1 = Deck::factory()->create(['utilisateur_id' => $utilisateur1->id]);
        $deck2 = Deck::factory()->create(['utilisateur_id' => $utilisateur2->id]);
        $deck3 = Deck::factory()->create(['utilisateur_id' => $utilisateur3->id]);

        $partie = Partie::factory()->create();
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck1->id, 'validee' => 1, 'position' => 1]);
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck2->id, 'validee' => 1, 'position' => 2]);
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck3->id, 'validee' => 0, 'position' => 1]);

        $this->actingAs($utilisateur1);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck1->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['nb_parties_gagnees' => 1]);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck2->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['nb_parties_gagnees' => 0]);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck3->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['nb_parties_gagnees' => 0]);
    });

    test('Calcule le pourcentage utilisation' , function () {
        $utilisateur1 = Utilisateur::factory()->create();
        $deck1 = Deck::factory()->create(['utilisateur_id' => $utilisateur1->id]);
        $deck2 = Deck::factory()->create(['utilisateur_id' => $utilisateur1->id]);
        $deck3 = Deck::factory()->create(['utilisateur_id' => $utilisateur1->id]);
        $deck4 = Deck::factory()->create(['utilisateur_id' => $utilisateur1->id]);

        $partie = Partie::factory()->create();
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck1->id, 'validee' => 1, 'position' => 1]);
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck2->id, 'validee' => 1, 'position' => 2]);
        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck3->id, 'validee' => 0, 'position' => 1]);

        $this->actingAs($utilisateur1);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck1->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['pourcentage_utilisation' => 50]);

        PartieDeck::factory()->create(['partie_id' => $partie->id, 'deck_id' => $deck4->id, 'validee' => 1, 'position' => 2]);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck1->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['pourcentage_utilisation' => 33]);

        $response = $this->get("/commander-ledger/utilisateurs/{$utilisateur1->id}/decks/{$deck3->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['pourcentage_utilisation' => 0]);
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

        $response->assertStatus(404);
    });
});

describe("Test la route pour delete un deck", function () {
   it("Peut anonymisé un deck (soft delete)", function () {
       $this->seed();

       $deck = Deck::get()[0];
       $utilisateur = $deck->utilisateur;

       $this->actingAs($utilisateur);

       $response = $this->deleteJson("/commander-ledger/utilisateurs/{$utilisateur->id}/decks/{$deck->id}");
       $response->assertStatus(200);
       $response->assertJsonFragment(['supprime' => 1]);

       $deck = Deck::find($deck->id);
       assertEquals($utilisateur->id, $deck->utilisateur->id);
       assertEquals('Supprimé', $deck->nom);
       assertEquals(1, $deck->supprime);
       assertEquals(null, $deck->photo);
   });
});
