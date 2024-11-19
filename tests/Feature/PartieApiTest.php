<?php

use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('peut créer une partie', function () {
    $this->seed();
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
    $this->assertEquals(11, Partie::count());
    $this->assertEquals(2, PartieDeck::count());
});

it('necessite d\'être authentifié', function () {
    $this->seed();
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
});
