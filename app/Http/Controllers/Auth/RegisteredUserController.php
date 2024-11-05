<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UtilisateurRequest;
use App\Models\Utilisateur;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UtilisateurRequest $request): array
    {
        $user = Utilisateur::create($request->validated());

        event(new Registered($user));

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            "id" => $user->id,
            "nom" => $user->nom,
            "couriel" => $user->couriel,
            "token" => $token,
        ];
    }
}
