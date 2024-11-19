<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Models\Deck;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * Controleur des utilisateurs
 */
class ProfileController extends Controller
{
    /**
     * Envoi la liste des utilisateurs
     *
     * @return JsonResponse
     */
    public function indexUtilisateur(): JsonResponse
    {
        $utilisateurs = Utilisateur::withTrashed()->get();

        return response()->json(new UtilisateurCollection($utilisateurs));
    }

    /**
     * Renvoyer un utilisateur
     *
     * @param $id
     * @return JsonResponse
     */
    public function showUtilisateur($id): JsonResponse
    {
        $utilisateur = Utilisateur::findOrFail($id);

        return response()->json([
            'data' => new UtilisateurResource($utilisateur),
        ]);
    }

    /**
     * Modification d'un utilisateur
     *
     * @param Request $requete
     * @param $id
     * @return JsonResponse
     */
    public function updateUtilisateur(Request $requete, $id): JsonResponse
    {
        $donneesValide = $requete->validate([
            'nom' => 'required|string|unique:utilisateurs,nom,' . $id,
            'courriel' => 'required|string|email',
            'photo' => 'nullable|string|max:255',
            'prive' => 'required|boolean',
        ]);

        $donneesValide['prive'] = filter_var($donneesValide['prive'], FILTER_VALIDATE_BOOLEAN);

        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update($donneesValide);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => new UtilisateurResource($utilisateur),
        ]);
    }

    /**
     * Supression d'un utilisateur
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroyUtilisateur($id): JsonResponse
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $utilisateurNonModifie = $utilisateur->replicate();

        $utilisateur->update([
            'nom' => 'Inconnu' . $utilisateur->id,
            'courriel' => 'inconnu' . $utilisateur->id . '@example.com',
            'photo' => null,
            'prive' => true,
            'password' => bcrypt(Str::random()),
        ]);

        // Soft delete
        $utilisateur->delete();

        return response()->json([
            'message' => 'Utilisateur anonymisé et désactivé avec succès.',
            'data' => new UtilisateurResource($utilisateurNonModifie),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Récupère les decks d'un utilisateur
     *
     * @param $id int id de l'utilisateur
     * @return JsonResponse les decks de l'utilisateur
     */
    public function indexDeck($id): JsonResponse
    {
        if (!ctype_digit((string)$id)) {
            return response()->json([
                'message' => 'Bad Request',
            ], 400);
        }

        $utilisateur = Utilisateur::findOrFail($id);

        $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();

        return response()->json([
            'data' => $decks,
        ]);
    }

    /**
     * Récupère un deck d'un utilisateur
     *
     * @param $id int id de l'utilisateur
     * @param $deckId int id du deck
     * @return JsonResponse le deck de l'utilisateur
     */
    public function showDeck($id, $deckId): JsonResponse
    {
        if (!ctype_digit((string)$id)) {
            return response()->json([
                'message' => 'Bad Request',
            ], 400);
        }

        if (!ctype_digit((string)$deckId)) {
            return response()->json([
                'message' => 'Bad Request',
            ], 400);
        }

        $deck = Deck::where('utilisateur_id', (int)$id)
            ->where('id', (int)$deckId)
            ->firstOrFail();

        return response()->json(['data' => $deck]);
    }
}
