<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a new user', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password123'),
    ];

    $response = $this->post('/users', $userData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
});

it('can read a user', function () {
    $user = User::factory()->create();

    $response = $this->get("/users/{$user->id}");

    $response->assertStatus(200);
    $response->assertJsonFragment(['email' => $user->email]);
});

it('can update a user', function () {
    $user = User::factory()->create();

    $updateData = [
        'name' => 'Jane Doe',
        'email' => 'janedoe@example.com',
    ];

    $response = $this->put("/users/{$user->id}", $updateData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
});

it('can delete a user', function () {
    $user = User::factory()->create();

    $response = $this->delete("/users/{$user->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
