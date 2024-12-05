<?php

use App\Models\Partie;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class, RefreshDatabase::class);

describe('Test le model d\'une partie', function () {
    test('La table associée au modèle est créée', function () {
        assertTrue(Schema::hasTable('parties'));
    });

    test('Le modèle à les attributs fillable corrects', function () {
        $partie = new Partie();
        $valeursRemplissablesAttendues = [
            'date',
            'nb_participants',
            'terminee',
            'gagnant_id',
            'createur_id'
        ];

        expect($partie->getFillable())->toEqual($valeursRemplissablesAttendues);
    });

    test('Le modèle a une relation avec un utilisateur "createur"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $partie = Partie::create([
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'terminee' => true,
            'createur_id' => $utilisateur->id
        ]);

        expect($partie->createur())->toBeInstanceOf(BelongsTo::class)
            ->and($partie->createur)->toBeInstanceOf(Utilisateur::class)
            ->and($partie->createur->id)->toBe($utilisateur->id);
    });

    test('Le modèle a une relation avec un utilisateur "gagnant"', function () {
        $utilisateur = Utilisateur::factory()->create();
        $partie = Partie::create([
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'terminee' => true,
            'createur_id' => $utilisateur->id,
            'gagnant_id' => $utilisateur->id
        ]);

        expect($partie->createur())->toBeInstanceOf(BelongsTo::class)
            ->and($partie->gagnant)->toBeInstanceOf(Utilisateur::class)
            ->and($partie->gagnant->id)->toBe($utilisateur->id);

        $partie2 = Partie::create([
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'terminee' => true,
            'createur_id' => $utilisateur->id,
            'gagnant_id' => null
        ]);

        expect($partie2->gagnant())->toBeInstanceOf(BelongsTo::class)
            ->and($partie2->gagnant)->toBeNull();
    });

    it('ne créer pas la partie si les champs nécessaires ne sont pas fournits', function () {
        try {
            Partie::create();
        } catch (QueryException $e) {
            $this->assertDatabaseMissing('parties');
        }
    });

    it('créée la partie avec les bonnes valeurs', function () {
        $utilisateur = Utilisateur::factory()->create();

        $partie = Partie::create([
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'terminee' => true,
            'createur_id' => $utilisateur->id
        ]);

        $this->assertDatabaseHas('parties', [
            'id' => $partie->id,
            'date' => '2020-01-01',
            'nb_participants' => 3,
            'terminee' => true,
            'createur_id' => $utilisateur->id,
            'gagnant_id' => null,
        ]);
    });
});
