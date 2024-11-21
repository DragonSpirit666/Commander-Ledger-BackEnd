<?php

use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class, RefreshDatabase::class);

describe('Test le model d\'une partie-deck', function () {
    test('La table associée au modèle est créée', function () {
        assertTrue(Schema::hasTable('parties_decks'));
    });

    test('Le modèle à les attributs fillable corrects', function () {
        $partieDeck = new PartieDeck();
        $valeursRemplissablesAttendues = [
            'validee',
            'position',
            'partie_id',
            'deck_id'
        ];

        expect($partieDeck->getFillable())->toEqual($valeursRemplissablesAttendues);
    });

    test('Le modèle a une relation avec un deck', function () {
        $partie = Partie::factory()->create();
        $deck = Deck::factory()->create();

        $partieDeck = PartieDeck::create([
            'position' => 2,
            'validee' => true,
            'partie_id' => $partie->id,
            'deck_id' => $deck->id,
        ]);

        expect($partieDeck->deck())->toBeInstanceOf(BelongsTo::class)
            ->and($partieDeck->deck)->toBeInstanceOf(Deck::class)
            ->and($partieDeck->deck->id)->toBe($deck->id);
    });

    test('Le modèle a une relation avec une partie', function () {
        $partie = Partie::factory()->create();
        $deck = Deck::factory()->create();

        $partieDeck = PartieDeck::create([
            'position' => 2,
            'validee' => true,
            'partie_id' => $partie->id,
            'deck_id' => $deck->id,
        ]);

        expect($partieDeck->partie())->toBeInstanceOf(BelongsTo::class)
            ->and($partieDeck->partie)->toBeInstanceOf(Partie::class)
            ->and($partieDeck->partie->id)->toBe($partie->id);
    });

    it('ne créer pas la partie-deck si les champs nécessaires ne sont pas fournits', function () {
        try {
            PartieDeck::create();
        } catch (QueryException $e) {
            $this->assertDatabaseMissing('parties_decks');
        }
    });

    it('créée la partie-deck avec les bonnes valeurs', function () {
        $partie = Partie::factory()->create();
        $deck = Deck::factory()->create();

        $partieDeck = PartieDeck::create([
            'position' => 2,
            'validee' => true,
            'partie_id' => $partie->id,
            'deck_id' => $deck->id,
        ]);

        $this->assertDatabaseHas('parties_decks', [
            'id' => $partieDeck->id,
            'validee' => true,
            'partie_id' => $partie->id,
            'deck_id' => $deck->id,
        ]);
    });
});
