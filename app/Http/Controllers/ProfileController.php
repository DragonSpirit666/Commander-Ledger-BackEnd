<?php

namespace App\Http\Controllers;

use App\Http\Logique\CreationDeck;
use App\Http\Requests\AjoutDeckRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Models\Deck;
use App\Models\Utilisateur;
use http\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Http\Logique\APIExterne;
use App\Http\Logique\CompterCouleur;
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
        // Find the utilisateur (user) or fail
        $data = $request->validated();
        $data['utilisateur_id'] = (int)$id;

        $deck = CreationDeck::creerDeck($data, $id);
        /*
        // faire les calcul
        $lignes = explode("\n", $data["cartes"]);
        $cartes = array();


        foreach ($lignes as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (!preg_match('/^(\d+)\s+(.*)$/', $line, $matches)) {
                return response()->json([
                    'error' => "Invalid card format: '{$line}'. Expected format: '<quantity> <card name>'."
                ], 400);
            }

            $quantity = intval($matches[1]);
            $cardName = $matches[2];
            $cartes[$cardName] = $quantity;
        }

        if (empty($cartes)) {
            return response()->json(['error' => 'Le paramêtre cartes dois contenir aumoin une carte valide.'], 400);
        }

        $deck = Deck::create($data);
        $deck->utilisateur()->associate($id);

        $cartesDetails = array();
        foreach ($cartes as $cardName => $quantity) {
            $apiResponse = APIExterne::AppelleAPICartes($cardName);
            // Decode the JSON response into an array
            $decodedResponse = json_decode($apiResponse, true);

            // A TESTER AVEC LES ERREUR ENVOYER PAR SCRYFALL
            if (empty($decodedResponse) || !isset($decodedResponse['prices']['usd'])) {
                return response()->json([
                    'error' => "Failed to fetch details for card: '{$cardName}'."
                ], 400);
            }

            $cartesDetails[] = [
                'carte_nom' => $cardName,
                'quantité' => $quantity,
                'couleurs' => $decodedResponse['colors'],
                'prix' => $decodedResponse['prices']["usd"]
            ];
        }

        $tauxCouleurs = json_decode(CompterCouleur::Compte($cartesDetails));

        foreach ($tauxCouleurs as $couleur => $prc) {
            switch ($couleur) {
                case "Blaqnches":
                    $deck->pourcentage_cartes_blanches = $prc;
                    break;
                case "Bleus":
                    $deck->pourcentage_cartes_bleues = $prc;
                    break;
                case "SansCouleur":
                    $deck->pourcentage_cartes_sans_couleur = $prc;
                    break;
                case "Rouges":
                    $deck->pourcentage_cartes_rouges = $prc;
                    break;
                case "Noirs":
                    $deck->pourcentage_cartes_noires = $prc;
                    break;
                case "Vertes":
                    $deck->pourcentage_cartes_vertes = $prc;
                    break;
            }
        }

        // CONTINUER ICI AVEC CHAT
        $prixTotal = 0;
        foreach ($cartesDetails as $carte) {
            $prixTotal += $carte['prix'];
        }

        $deck->prix = $prixTotal;

        $deck->save();
        */
        // sinon créer les deck ressources pour le renvoyer en json et confirmer l'ajout*/
        return response()->json([$deck], 201);
    }
}
