<?php

use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);

describe('Test la route pour créer une partie', function () {
    it('peut créer une partie', function () {
        $this->refreshDatabase();
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(200);
        $this->assertEquals($nbParties + 1, Partie::count());
        $this->assertEquals($nbPartiesDecks + 2, PartieDeck::count());
    });

    it('necessite d\'être authentifié', function () {
        $this->refreshDatabase();
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);
        $response->assertStatus(401);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());
    });

    it('retourne un erreur 422 si un champ n\'est pas présent ou valide', function () {
        $this->refreshDatabase();
        $this->seed();
        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $partieInfo = [
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
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
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());
    });

    it("Ne peut créer une partie avec des positions ou des decks en double", function () {
        $this->refreshDatabase();
        $this->seed();

        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $deck = Deck::get()[0];
        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => $deck->id, "position" => 1],
                ["deck_id" => $deck->id, "position" => 2],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
                ["deck_id" => Deck::get()[1]->id, "position" => 1],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);

        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());
    });

    it("Doit avoir entre 2 et 8 participants", function () {
        $this->refreshDatabase();
        $this->seed();
        $utilisateur = Utilisateur::get()[0];
        $this->actingAs($utilisateur);

        $nbParties = Partie::count();
        $nbPartiesDecks = PartieDeck::count();

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
            ],
        ];

        $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties', $partieInfo);
        $response->assertStatus(422);
        $this->assertEquals($nbParties, Partie::count());
        $this->assertEquals($nbPartiesDecks, PartieDeck::count());

        $partieInfo = [
            "date" => date('Y/m/d'),
            "participants" => [
                ["deck_id" => Deck::get()[0]->id, "position" => 1],
                ["deck_id" => Deck::get()[1]->id, "position" => 2],
                ["deck_id" => Deck::get()[2]->id, "position" => 3],
                ["deck_id" => Deck::get()[3]->id, "position" => 4],
                ["deck_id" => Deck::get()[4]->id, "position" => 5],
                ["deck_id" => Deck::get()[5]->id, "position" => 6],
                ["deck_id" => Deck::get()[6]->id, "position" => 7],
                ["deck_id" => Deck::get()[7]->id, "position" => 8],
                ["deck_id" => Deck::get()[8]->id, "position" => 9],
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
        $this->refreshDatabase();
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
        $this->refreshDatabase();
        $this->seed();
        $utilisateur = Utilisateur::get()[0];
        $partie = Partie::get()[0];

        $this->actingAs($utilisateur);

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/'.$partie->id);

        $response->assertStatus(200);
    });

    it('necessite d\'être authentifié', function () {
        $this->refreshDatabase();
        $this->seed();
        $utilisateur = Utilisateur::get()[0];
        $partie = Partie::get()[0];

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/'.$partie->id);
        $response->assertStatus(401);
    });

    it('retourne 404 si la partie n\'existe pas', function () {
        $this->refreshDatabase();
        $this->seed();
        $utilisateur = Utilisateur::get()[0];

        $this->actingAs($utilisateur);

        $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/9385');
        $response->assertStatus(404);
    });
});

describe("Test la route pour get les invitations à des parties", function() {
   it("Peut récupérer les invitations non validées", function() {
       $this->refreshDatabase();
       $this->seed();

       $utilisateur = Utilisateur::get()[0];
       $this->actingAs($utilisateur);

       $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/parties/invitations');
       $response->assertStatus(200);
   });

   it("Ne renvoit pas les parties validées", function() {
       $this->refreshDatabase();
       $this->seed();
       $this->actingAs(Utilisateur::get()[0]);

       $partieDeck = PartieDeck::get()[0];
       $utilisateurNotifier = Deck::find($partieDeck->deck_id)->utilisateur;

       $decks = Deck::where('utilisateur_id', $utilisateurNotifier->id);
       $invitations = PartieDeck::wherein('deck_id', $decks->pluck('id'))->get();

       $partieDeck->update(['validee' => true]);

       $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations');
       $response->assertStatus(200)->assertJsonCount($invitations->count() - 1, 'data');
   });
});

describe("Test la route pour répondre à une invitation de partie", function() {
    test("Peut accetper une invitation", function() {
        $this->refreshDatabase();
        $this->seed();
        $this->actingAs(Utilisateur::get()[0]);

        $partieDeck = PartieDeck::get()[0];
        $utilisateurNotifier = Deck::find($partieDeck->deck_id)->utilisateur;

        $decks = Deck::where('utilisateur_id', $utilisateurNotifier->id);
        $invitationsAvant = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();

        $response = $this->putJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations/'.$partieDeck->id, ['invitation_acceptee' => true]);
        $response->assertStatus(200);

        $invitationsApres = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();
        assertEquals($invitationsAvant->count() + 1, count($invitationsApres));
    });

    test("Peut refuser une invitation", function() {
        $this->refreshDatabase();
        $this->seed();
        $this->actingAs(Utilisateur::get()[0]);

        $partieDeck = PartieDeck::get()[0];
        $utilisateurNotifier = Deck::find($partieDeck->deck_id)->utilisateur;

        $decks = Deck::where('utilisateur_id', $utilisateurNotifier->id);
        $invitationsAvant = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();

        $response = $this->putJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations/'.$partieDeck->id, ['invitation_acceptee' => false]);
        $response->assertStatus(200);

        $invitationsApres = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();
        assertEquals(count($invitationsAvant), count($invitationsApres));
    });

    test("Ne peut répondre à une invitation déjà validée", function() {
        $this->refreshDatabase();
        $this->seed();
        $this->actingAs(Utilisateur::get()[0]);

        $partieDeck = PartieDeck::get()[0];
        $utilisateurNotifier = Deck::find($partieDeck->deck_id)->utilisateur;

        $decks = Deck::where('utilisateur_id', $utilisateurNotifier->id);
        $invitationsAvant = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();

        $this->putJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations/'.$partieDeck->id, ['invitation_acceptee' => false]);
        $response = $this->putJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations/'.$partieDeck->id, ['invitation_acceptee' => true]);
        $response->assertStatus(404);

        $invitationsApres = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();
        assertEquals(count($invitationsAvant), count($invitationsApres));
    });

    test("Le champ invitation_acceptee est requis", function() {
        $this->refreshDatabase();
        $this->seed();
        $this->actingAs(Utilisateur::get()[0]);

        $partieDeck = PartieDeck::get()[0];
        $utilisateurNotifier = Deck::find($partieDeck->deck_id)->utilisateur;

        $decks = Deck::where('utilisateur_id', $utilisateurNotifier->id);
        $invitationsAvant = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();

        $response = $this->putJson('/commander-ledger/utilisateurs/'.$utilisateurNotifier->id.'/parties/invitations/'.$partieDeck->id);
        $response->assertStatus(422);

        $invitationsApres = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->get();
        assertEquals(count($invitationsAvant), count($invitationsApres));
    });
});
