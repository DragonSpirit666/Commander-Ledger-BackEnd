<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Utilisation du trait RefreshDatabase pour s'assurer que la base de données est restaurée avant chaque test
uses(RefreshDatabase::class);

// Test pour vérifier la création d'un nouvel utilisateur
it('can create a new user', function () {

    // Données pour le nouvel utilisateur à créer
    $userData = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password123'), // Cryptage du mot de passe
    ];

    // Envoi d'une requête POST pour créer un utilisateur
    $response = $this->post('/users', $userData);

    // Vérification que la requête a réussi avec le statut HTTP 201 (Créé)
    $response->assertStatus(201);

    // Vérification que l'utilisateur existe dans la base de données
    $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
});

// Test pour vérifier la récupération d'un utilisateur existant
it('can read a user', function () {
    // Création d'un utilisateur en utilisant la factory
    $user = User::factory()->create();

    // Envoi d'une requête GET pour récupérer les informations de l'utilisateur
    $response = $this->get("/users/{$user->id}");

    // Vérification que la requête a réussi avec le statut HTTP 200 (OK)
    $response->assertStatus(200);

    // Vérification que la réponse JSON contient l'email de l'utilisateur
    $response->assertJsonFragment(['email' => $user->email]);
});

// Test pour vérifier la mise à jour d'un utilisateur existant
it('can update a user', function () {
    // Création d'un utilisateur en utilisant la factory
    $user = User::factory()->create();

    // Données de mise à jour pour l'utilisateur
    $updateData = [
        'name' => 'Jane Doe',
        'email' => 'janedoe@example.com',
    ];

    // Envoi d'une requête PUT pour mettre à jour les informations de l'utilisateur
    $response = $this->put("/users/{$user->id}", $updateData);

    // Vérification que la requête a réussi avec le statut HTTP 200 (OK)
    $response->assertStatus(200);

    // Vérification que les informations de l'utilisateur ont été mises à jour dans la base de données
    $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
});

// Test pour vérifier la suppression d'un utilisateur existant
it('can delete a user', function () {
    // Création d'un utilisateur en utilisant la factory
    $user = User::factory()->create();

    // Envoi d'une requête DELETE pour supprimer l'utilisateur
    $response = $this->delete("/users/{$user->id}");

    // Vérification que la requête a réussi avec le statut HTTP 204 (Pas de contenu)
    $response->assertStatus(204);

    // Vérification que l'utilisateur n'existe plus dans la base de données
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
