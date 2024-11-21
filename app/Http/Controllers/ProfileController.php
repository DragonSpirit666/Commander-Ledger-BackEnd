<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjoutDeckRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Models\Deck;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

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

    public function ajouterDeck(AjoutDeckRequest $request, $id) : JsonResponse
    {
        $deck = Deck::create($request->validated());

        //plus sur
        $deck->utilisateur()->associate($id);

        // faire les calcul
        $lignes = explode("\n", $deck->cartes);

        $cartes = array();

        foreach ($lignes as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (preg_match('/^(\d+)\s+(.*)$/', $line, $matches)) {
                $quantity = intval($matches[1]);
                $cardName = $matches[2];

                // Step 3: Add to the associative array
                $cartes[$cardName] = $quantity;
            }
        }

        $cartesDetails = array();
        foreach ($cartesDetails as $cardName) {
            // faire les call pour les cartes
        }

        foreach ($cartes as $cardName => $quantity) {
            // faire les call pour les cartes
        }

        // voir pour assigner le deck a l'utilisateur si s'est pas fait automatiquement
        // sinon créer les deck ressources pour le renvoyer en json et confirmer l'ajout
    }
}
