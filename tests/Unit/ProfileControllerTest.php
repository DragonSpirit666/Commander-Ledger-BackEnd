<?php

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Testing\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->utilisateur = Utilisateur::factory()->create();
});

it('peut récupérer tous les utilisateurs', function () {
    $response = $this->getJson('/api/utilisateurs');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'nom',
                'courriel',
                'photo',
                'prive',
            ]
        ]
    ]);
});

it('peut mettre à jour un utilisateur', function () {
    $response = $this->putJson('/api/utilisateurs/' . $this->utilisateur->id, [
        'nom' => 'Nouveau Nom',
        'courriel' => 'nouveau@exemple.com',
        'prive' => true
    ]);
    $response->assertStatus(200);
    $response->assertJsonFragment(['message' => 'Utilisateur mis à jour avec succès']);
});

it('peut supprimer un utilisateur', function () {
    $response = $this->deleteJson('/api/utilisateurs/' . $this->utilisateur->id);
    $response->assertStatus(200);
    $response->assertJsonFragment(['message' => 'Utilisateur anonymisé et désactivé avec succès.']);
});

