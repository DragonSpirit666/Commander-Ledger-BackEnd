<?php

use App\Models\Ami;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

it('accepte une demande d\'ami', function () {
    $this->seed();
    $utilisateur1 = Utilisateur::first();
    $utilisateur2 = Utilisateur::skip(1)->first();

    $ami = Ami::firstOrCreate([
        'utilisateur_demandeur_id' => $utilisateur1->id,
        'utilisateur_receveur_id' => $utilisateur2->id,
        'invitation_accepter' => false,
    ]);

    $this->actingAs($utilisateur2);

    $response = $this->putJson(
        '/commander-ledger/utilisateurs/'.$utilisateur2->id.'/amis/demandes/'.$utilisateur1->id,
        ['invitation_accepter' => true]);

    $response->assertStatus(200)
    ->assertJson(['message' => 'Demande d\'ami acceptée.']);

    $ami->refresh();
        $this->assertEquals(true, $ami->invitation_accepter);
});

it('obtient la liste d\'amis d\'un utlisateur', function () {
    $this->seed();

    $utilisateur = Utilisateur::first();
    $ami1 = Utilisateur::skip(1)->first();
    $ami2 = Utilisateur::skip(2)->first();
    $nonAmi = Utilisateur::factory()->create();

    $this->actingAs($utilisateur);

    Ami::create([
        'utilisateur_demandeur_id' => $utilisateur->id,
        'utilisateur_receveur_id' => $ami1->id,
        'invitation_accepter' => true,
    ]);

    Ami::create([
        'utilisateur_demandeur_id' => $utilisateur->id,
        'utilisateur_receveur_id' => $ami2->id,
        'invitation_accepter' => true,
    ]);

    Ami::create([
        'utilisateur_demandeur_id' => $utilisateur->id,
        'utilisateur_receveur_id' => $nonAmi->id,
        'invitation_accepter' => false,
    ]);

    $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur->id.'/amis');

    $response->assertStatus(200);
    $amis = $response->json();

    $this->assertCount(2, $amis);
    $this->assertTrue(collect($amis)->contains('utilisateur_receveur_id', $ami1->id));
    $this->assertTrue(collect($amis)->contains('utilisateur_receveur_id', $ami2->id));
    $this->assertFalse(collect($amis)->contains('utilisateur_receveur_id', $nonAmi->id));
});


it('ne permet pas d\'envoyer une demande d\'ami à soi-même', function () {
    $utilisateur1 = Utilisateur::factory()->create();
    $this->actingAs($utilisateur1);

    $response = $this->postJson('/commander-ledger/utilisateurs/'.$utilisateur1->id.'/amis/demandes/'.$utilisateur1->id);

    $response->assertStatus(400)
        ->assertJson(['message' => 'Tu ne peux pas envoyer une demande d\'ami à toi-même.']);
});

it('retourne la liste des demandes d\'ami en attente', function () {
    $utilisateur1 = Utilisateur::factory()->create(); // Demandeur
    $utilisateur2 = Utilisateur::factory()->create(); // Receveur

    Ami::create([
        'utilisateur_demandeur_id' => $utilisateur1->id,
        'utilisateur_receveur_id' => $utilisateur2->id,
        'invitation_accepter' => false,
    ]);

    $this->actingAs($utilisateur1);

    $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur1->id.'/amis/demandes');

    $response->assertStatus(200)
        ->assertJsonCount(1);
});

// Test pas utiliser puisque route effacer
/*it('retourne la liste des demandes d\'acceptation d\'ami en attente', function () {
    $utilisateur1 = Utilisateur::factory()->create(); // Demandeur
    $utilisateur2 = Utilisateur::factory()->create(); // Receveur

    $this->actingAs($utilisateur2);

    Ami::create([
        'utilisateur_demandeur_id' => $utilisateur1->id,
        'utilisateur_receveur_id' => $utilisateur2->id,
        'invitation_accepter' => false,
    ]);

    $response = $this->getJson('/commander-ledger/utilisateurs/'.$utilisateur2->id.'/amis/acceptations-en-attente');

    $response->assertStatus(200)
        ->assertJsonCount(1);
});*/

it('supprime une demande d\'ami ou une amitié', function () {
    $utilisateur1 = Utilisateur::factory()->create(); // Demandeur
    $utilisateur2 = Utilisateur::factory()->create(); // Receveur

    $ami = Ami::create([
        'utilisateur_demandeur_id' => $utilisateur1->id,
        'utilisateur_receveur_id' => $utilisateur2->id,
        'invitation_accepter' => false,
    ]);

    $this->actingAs($utilisateur1);

    $response = $this->deleteJson('/commander-ledger/utilisateurs/'.$utilisateur1->id.'/amis/'.$utilisateur2->id);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Amitié détruit avec succès.']);

    $this->assertDatabaseMissing('amis', [
        'utilisateur_demandeur_id' => $utilisateur1->id,
        'utilisateur_receveur_id' => $utilisateur2->id,
    ]);
});




