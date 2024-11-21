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
            'user_1_id',
            'user_2_id',
            'invitation_accepter'
        ];

        expect($ami->getFillable())->toEqual($valeursRemplissablesAttendues);
    });

    test('Le modèle a une relation avec un utilisateur "user_1"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $ami = Ami::create([
            'user_1_id' => $utilisateur->id,
            'user_2_id' => Utilisateur::factory()->create()->id,
            'invitation_accepter' => false
        ]);

        expect($ami->utilisateur1())->toBeInstanceOf(BelongsTo::class)
            ->and($ami->utilisateur1)->toBeInstanceOf(Utilisateur::class)
            ->and($ami->utilisateur1->id)->toBe($utilisateur->id);
    });

    test('Le modèle a une relation avec un utilisateur "user_2"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $ami = Ami::create([
            'user_1_id' => $utilisateur->id,
            'user_2_id' => Utilisateur::factory()->create()->id,
            'invitation_accepter' => false
        ]);

        expect($ami->utilisateur2())->toBeInstanceOf(BelongsTo::class)
            ->and($ami->utilisateur2)->toBeInstanceOf(Utilisateur::class);
    });

    test('Le modèle accepte l\'invitation d\'ami', function () {
        $utilisateur1 = Utilisateur::factory()->create();
        $utilisateur2 = Utilisateur::factory()->create();
        $ami = Ami::create([
            'user_1_id' => $utilisateur1->id,
            'user_2_id' => $utilisateur2->id,
            'invitation_accepter' => false
        ]);

        // Accepter la demande
        $ami->invitation_accepter = true;
        $ami->save();

        $this->assertDatabaseHas('amis', [
            'user_1_id' => $utilisateur1->id,
            'user_2_id' => $utilisateur2->id,
            'invitation_accepter' => true
        ]);
    });

    it('ne crée pas une demande d\'ami si les champs nécessaires ne sont pas fournis', function () {
        try {
            Ami::create();
        } catch (QueryException $e) {
            $this->assertDatabaseMissing('amis', [
                'user_1_id' => null,
                'user_2_id' => null
            ]);
        }
    });

    it('créée une demande d\'ami avec les bonnes valeurs', function () {
        $utilisateur1 = Utilisateur::factory()->create();
        $utilisateur2 = Utilisateur::factory()->create();

        $ami = Ami::create([
            'user_1_id' => $utilisateur1->id,
            'user_2_id' => $utilisateur2->id,
            'invitation_accepter' => false
        ]);

        $this->assertDatabaseHas('amis', [
            'user_1_id' => $utilisateur1->id,
            'user_2_id' => $utilisateur2->id,
            'invitation_accepter' => false
        ]);
    });
});
