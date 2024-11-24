<?php

use App\Models\Ami;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class, RefreshDatabase::class);

describe('Test le modèle Ami', function () {
    test('La table associée au modèle est créée', function () {
        assertTrue(Schema::hasTable('amis'));
    });

    test('Le modèle a les attributs fillable corrects', function () {
        $ami = new Ami();
        $valeursRemplissablesAttendues = [
            'utilisateur_demandeur_id',
            'utilisateur_receveur_id',
            'invitation_accepter'
        ];

        expect($ami->getFillable())->toEqual($valeursRemplissablesAttendues);
    });

    test('Le modèle a une relation avec un utilisateur "utilisateur_demandeur"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $ami = Ami::create([
            'utilisateur_demandeur_id' => $utilisateur->id,
            'utilisateur_receveur_id' => Utilisateur::factory()->create()->id,
            'invitation_accepter' => false
        ]);

        expect($ami->utilisateurDemandeur())->toBeInstanceOf(BelongsTo::class)
            ->and($ami->utilisateurDemandeur)->toBeInstanceOf(Utilisateur::class)
            ->and($ami->utilisateurDemandeur->id)->toBe($utilisateur->id);
    });

    test('Le modèle a une relation avec un utilisateur "utilisateur_receveur"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $ami = Ami::create([
            'utilisateur_demandeur_id' => $utilisateur->id,
            'utilisateur_receveur_id' => Utilisateur::factory()->create()->id,
            'invitation_accepter' => false
        ]);

        expect($ami->utilisateurReceveur())->toBeInstanceOf(BelongsTo::class)
            ->and($ami->utilisateurReceveur)->toBeInstanceOf(Utilisateur::class);
    });

    test('Le modèle accepte l\'invitation d\'ami', function () {
        $utilisateurDemandeur = Utilisateur::factory()->create();
        $utilisateurReceveur = Utilisateur::factory()->create();
        $ami = Ami::create([
            'utilisateur_demandeur_id' => $utilisateurDemandeur->id,
            'utilisateur_receveur_id' => $utilisateurReceveur->id,
            'invitation_accepter' => false
        ]);

        // Accepter la demande
        $ami->invitation_accepter = true;
        $ami->save();

        $this->assertDatabaseHas('amis', [
            'utilisateur_demandeur_id' => $utilisateurDemandeur->id,
            'utilisateur_receveur_id' => $utilisateurReceveur->id,
            'invitation_accepter' => true
        ]);
    });

    it('ne crée pas une demande d\'ami si les champs nécessaires ne sont pas fournis', function () {
        try {
            Ami::create();
        } catch (QueryException $e) {
            $this->assertDatabaseMissing('amis', [
                'utilisateur_demandeur_id' => null,
                'utilisateur_receveur_id' => null
            ]);
        }
    });

    it('créée une demande d\'ami avec les bonnes valeurs', function () {
        $utilisateurDemandeur = Utilisateur::factory()->create();
        $utilisateurReceveur = Utilisateur::factory()->create();

        $ami = Ami::create([
            'utilisateur_demandeur_id' => $utilisateurDemandeur->id,
            'utilisateur_receveur_id' => $utilisateurReceveur->id,
            'invitation_accepter' => false
        ]);

        $this->assertDatabaseHas('amis', [
            'utilisateur_demandeur_id' => $utilisateurDemandeur->id,
            'utilisateur_receveur_id' => $utilisateurReceveur->id,
            'invitation_accepter' => false
        ]);
    });

    it('récupère les amis acceptés', function () {
        $this->seed();

        $utilisateur = Utilisateur::first();
        $ami1 = Utilisateur::skip(1)->first();
        $ami2 = Utilisateur::skip(2)->first();
        $nonAmi = Utilisateur::factory()->create();

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

        $amis = $utilisateur->amisAccepter();


        expect($amis->contains(fn($ami) => $ami->utilisateur_receveur_id === $ami1->id))->toBeTrue();
        expect($amis->contains(fn($ami) => $ami->utilisateur_receveur_id === $ami2->id))->toBeTrue();
        expect($amis->contains(fn($ami) => $ami->utilisateur_receveur_id === $nonAmi->id))->toBeFalse();
    });
});
