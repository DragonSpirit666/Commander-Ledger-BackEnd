<?php

use App\Http\Logique\CreationDeck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Deck;
use App\Models\Utilisateur;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);


describe("Test effectué sur la création d'un deck", function () {

    test("La création d'un deck avec paramêtres valide", function () {

        $user = Utilisateur::factory()->create();

        $parametres = [
            'nom' => "Pest test1",
            'cartes' => "4 Lightning Bolt\n2 Giant Growth\n1 Island",
        ];


        $deck = CreationDeck::creerDeck($parametres, $user->id);

        expect($deck)->toBeInstanceOf(Deck::class)
            ->and($deck->nom)->toBe('Pest test1')
            ->and($deck->prix)->not->toBeNull()
            ->and($deck->pourcentage_cartes_blanches)->not->toBeNull()
            ->and($deck->pourcentage_cartes_rouges)->not->toBeNull();

        // Assert the deck exists in the database
        $this->assertDatabaseHas('decks', [
            'id' => $deck->id,
            'nom' => 'Pest test1',
            'utilisateur_id' => $user->id,
        ]);
    });

    test("La création d'un deck avec paramêtres valide et paramêtres optionnel", function () {

        $user = Utilisateur::factory()->create();

        $parametres = [
            'nom' => "Pest test2",
            'cartes' => "4 Lightning Bolt\n2 Giant Growth\n1 Island",
            'salt' => 1,
            'prix' => 50
        ];


        $deck = CreationDeck::creerDeck($parametres, $user->id);

        expect($deck)->toBeInstanceOf(Deck::class)
            ->and($deck->nom)->toBe('Pest test2')
            ->and($deck->prix)->toBe(50)
            ->and($deck->salt)->toBe(1)
            ->and($deck->pourcentage_cartes_blanches)->not->toBeNull()
            ->and($deck->pourcentage_cartes_rouges)->not->toBeNull();

        // Assert the deck exists in the database
        $this->assertDatabaseHas('decks', [
            'id' => $deck->id,
            'nom' => 'Pest test2',
            'utilisateur_id' => $user->id,
            'salt' => 1,
            'prix' => 50
        ]);


    });

    test("La création d'un deck avec paramêtres requis manquant", function () {
        $user = Utilisateur::factory()->create();

        // Test case 1: Missing "nom"
        $parametresNomManquant = [
            'cartes' => "4 Lightning Bolt\n2 Giant Growth\n1 Island",
        ];

        expect(fn() => CreationDeck::creerDeck($parametresNomManquant, $user->id))
            ->toThrow(\InvalidArgumentException::class, 'les paramêtres sont obligatoires');

        // Test case 2: Missing "cartes"
        $parametresCartesManquant = [
            'nom' => "Test Deck",
        ];

        expect(fn() => CreationDeck::creerDeck($parametresCartesManquant, $user->id))
            ->toThrow(\InvalidArgumentException::class, 'les paramêtres sont obligatoires');
    });

    test("La création d'un deck avec paramêtres invalides", function () {
        $user = Utilisateur::factory()->create();

        $parametres = [
            'nom' => "Pest test3",
            'cartes' => "paramètres invalide",
        ];

        expect(fn() => CreationDeck::creerDeck($parametres, $user->id))
            ->toThrow(\InvalidArgumentException::class, 'format de cartes invalide');

        $parametresMauvais = [
            "nom" => "Pest test4",
            "cartes" => "4 Carte qui existe pas\n2 Carte Random"
        ];

        expect(fn() => CreationDeck::creerDeck($parametresMauvais, $user->id))
            ->toThrow(\RuntimeException::class, ("Erreur lors de la requête API"));
    });

});
