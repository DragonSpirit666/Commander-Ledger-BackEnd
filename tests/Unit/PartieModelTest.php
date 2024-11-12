<?php

use App\Models\Partie;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class, RefreshDatabase::class);

describe('Test le model d\'une partie', function () {
    test('La table associée au modèle est créée', function () {
        assertTrue(Schema::hasTable('parties'));
    });

    it('ne créer pas la partie si les champs nécessaires ne sont pas fournits', function () {
        Partie::create([
            'date' => date('Y-m-d'),
            'nb_participants' => 5,
            'termine' => false,
            'createur_id' => 1,
            'gagnant_id' => 1,
        ]);
    });
});
