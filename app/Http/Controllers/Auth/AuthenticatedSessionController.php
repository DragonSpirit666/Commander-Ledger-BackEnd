<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): array
    {
        $request->authenticate();

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            "data" => [
                "utilisateur" => new UtilisateurResource($user),
                "token" => $token,
            ],
        ];
    }

    /**
     * Logout l'utilisateur
     */
    public function destroy(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Utilisateur deconnecte avec succes.',
            'data' => new UtilisateurResource($user),
        ]);
    }
}
