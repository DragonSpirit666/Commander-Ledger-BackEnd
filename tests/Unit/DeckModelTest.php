<?php

use App\Models\Utilisateur;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Deck;

uses(TestCase::class, RefreshDatabase::class);
/**
 * Les test suivent servent à assurer la bonne création d'un modèle.
 * que les attribues pouvant êtres remplis sont valides.
 * Finalement, que les relations entre les autres tables sont valides.
 */
describe('Test effectué sur le modèle d\'un Deck', function () {

    test('La table associée au modèle est créée', function () {
        expect(Schema::hasTable('decks'))->toBeTrue();
    });

    test('Le modèle a les attributs fillable corrects', function () {
        $deck = new Deck();
        $valeursRemplissables = [
            'nom',
            'photo',
            'cartes',
            'nb_parties_gagnees',
            'nb_parties_perdues',
            'prix',
            'salt',
            'pourcentage_utilisation',
            'supprime',
            'utilisateur_id',
            'pourcentage_cartes_bleues',
            'pourcentage_cartes_jaunes',
            'pourcentage_cartes_rouges',
            'pourcentage_cartes_noires',
            'pourcentage_cartes_vertes',
            'pourcentage_cartes_blanches',
        ];

        expect($deck->getFillable())->toEqual($valeursRemplissables);
    });

    test('Le modèle a une relation utilisateur', function () {
        $utilisateur = Utilisateur::factory()->create();

        $deck = Deck::factory()->create(['utilisateur_id' => $utilisateur->id]);

        expect($deck->utilisateur())->toBeInstanceOf(BelongsTo::class)
            ->and($deck->utilisateur)->toBeInstanceOf(Utilisateur::class)
            ->and($deck->utilisateur->id)->toBe($utilisateur->id);
    });

    test('ne créer pas le deck si les champs nécessaires ne sont pas fournits', function () {
        $this->expectException(QueryException::class);
        Deck::create();
    });

    test('créer le deck si les champs sont fournits', function () {
        $utilisateur  = Utilisateur::factory()->create();

        $deck = Deck::create([
            'nom' => 'test',
            'photo' => 'test',
            'cartes' => 'test',
            'nb_parties_gagnees' => 2,
            'nb_parties_perdues' => 2,
            'prix' => 23.43,
            'salt' => 2.34,
            'pourcentage_utilisation' => 2.37,
            'supprime' => false,
            'utilisateur_id' => $utilisateur->id,
            'pourcentage_cartes_bleues' => 20,
            'pourcentage_cartes_jaunes' => 20,
            'pourcentage_cartes_rouges' => 20,
            'pourcentage_cartes_vertes' => 20,
            'pourcentage_cartes_noires' => 20,
            'pourcentage_cartes_blanches' => 20
        ]);
        expect($deck)->toBeInstanceOf(Deck::class);
    });
});
