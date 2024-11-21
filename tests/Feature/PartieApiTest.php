<?php

use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Test la route pour créer une partie', function () {
    it('peut créer une partie', function () {
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(200);
        $this->assertEquals($nbParties + 1, Partie::count());
        $this->assertEquals($nbPartiesDecks + 2, PartieDeck::count());
    });

    it('necessite d\'être authentifié', function () {
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);
        $response->assertStatus(401);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());
    });

    it('retourne un erreur 422 si un champ n\'est pas présent ou valide', function () {
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $partieInfo = [
            "participants" => [
                ["deck_id" => Deck::get()[0]->id],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());

        $partieInfo = [
            "date" => date('y-m-d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());
    });
});

describe('Test la route pour get les parties d\'un utilisateur', function () {
    it('peut get les parties', function () {
        $this->seed();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties');

        $response->assertStatus(200);
    });

    it('necessite d\'être authentifié', function () {
        $this->seed();
        $utilisateur = Utilisateur::get()[0];

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties');
        $response->assertStatus(401);
    });
});

describe('Test la route pour get une partie', function () {
    it('peut get la partie', function () {
        $this->seed();
        $utilisateur = Utilisateur::get()[0];
        $partie = Partie::get()[0];

        $this->actingAs($utilisateur);

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/'.$partie->id);

        $response->assertStatus(200);
    });

    it('necessite d\'être authentifié', function () {
        $this->seed();
        $utilisateur = Utilisateur::get()[0];
        $partie = Partie::get()[0];

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/'.$partie->id);
        $response->assertStatus(401);
    });

    it('retourne 404 si la partie n\'existe pas', function () {
        $this->seed();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/9385');
        $response->assertStatus(404);
    });
});
