<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartieRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PartieCollection;
use App\Http\Resources\PartieResource;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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

        foreach ($utilisateurs as $utilisateur) {
            CalculerPrixTotalsDecksUtilisateur::Calc($utilisateur);
            dd($utilisateur);
        }

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

        $deck = Deck::where('id', (int)$deckId)
            ->firstOrFail();

        return response()->json(['data' => $deck]);
    }


    /**
     * Création d'une partie
     *
     * @param int $id id de l'utilisateur qui créer la partie
     * @param PartieRequest $request request avec les informations envoyés
     *
     * @return PartieResource information sur la partie créée
     */
    public function storePartie(int $id, PartieRequest $request) : PartieResource
    {
        $request->validated();

        $listeParticipants = $request->get('participants');
        $nbParticippants = count($listeParticipants);
        $terminee = $request->has('terminee') ? $request->get('terminee') : false;

        $partie = Partie::create([
            'date' => $request->get('date'),
            'nb_participants' => $nbParticippants,
            'terminee' => $terminee,
            'createur_id' => $id,
        ]);

        $listePartiesDecks = [];

        foreach ($listeParticipants as $participant) {
            $deck = Deck::find($participant['deck_id']);

            $partieDeck = PartieDeck::create([
                'partie_id' => $partie->id,
                'deck_id' => $participant['deck_id'],
                'position' => in_array('position', $participant) ? $participant['position'] : null,
            ]);

            $listePartiesDecks[] = $partieDeck;

            if ($terminee && $partieDeck->position == 1) {
                $partie->update(['gagnant_id' => $deck->utilisateur->id]);
            }
        }

        return new PartieResource([
            'id' => $partie->id,
            'date' => $partie->date,
            'nb_participants' => $nbParticippants,
            'terminee' => $terminee,
            'createur_id' => $id,
            'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
            'participants' => $listePartiesDecks,
        ]);
    }

    /**
     * Récupère toutes les parties associées à un utilisateur
     *
     * @param int $id id de l'utilisateur dont on veut les parties
     *
     * @return PartieCollection toutes les parties auquelles l'utilisateur est associé
     */
    public function indexPartie(int $id): PartieCollection
    {
        $decks = Deck::where('utilisateur_id', $id);
        $partiesDecksUtilisateur = PartieDeck::whereIn('deck_id', $decks->pluck('id'))->get();
        $parties = Partie::find($partiesDecksUtilisateur->pluck('partie_id'));

        $partiesDecksTotal = PartieDeck::whereIn('partie_id', $parties->pluck('id'))->get();

        $information = [];

        foreach ($parties as $partie) {
            $information[] = [
                'id' => $partie->id,
                'date' => $partie->date,
                'nb_participants' => $partie->nb_participants,
                'terminee' => $partie->terminee,
                'createur_id' => $partie->createur->id,
                'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
                'participants' => $partiesDecksTotal,
            ];
        }

        return new PartieCollection($information);
    }

    /**
     * Récupère une partie
     *
     * @param int $id id de l'utilisateur qui a fait la requête
     * @param int $partieId id de la partie à récupérer
     *
     * @return PartieResource partie trouvée
     */
    public function showPartie(int $id, int $partieId): PartieResource {
        $partie = Partie::find($partieId);

        if ($partie == null) {
            throw new NotFoundResourceException();
        }

        $partiesDecks = PartieDeck::where('partie_id', $partieId)->get();

        return new PartieResource([
            'id' => $partie->id,
            'date' => $partie->date,
            'nb_participants' => $partie->nb_participants,
            'terminee' => $partie->terminee,
            'createur_id' => $partie->createur->id,
            'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
            'participants' => $partiesDecks,
        ]);
    }

    /**
     * Supprime un deck (l'anonymise)
     *
     * @param int $id id du deck à supprimer
     * @return JsonResponse information du deck avant son anonymisation
     */
    public function deleteDeck(int $id, int $deckId): JsonResponse {
        $deck = Deck::findOrFail($deckId);

        $deck->update(['supprime' => 1]);
        $deckNonModifie = $deck->replicate();

        $deck->update([
            'nom' => 'Supprimé',
            'photo' => null,
            'cartes' => "",
            'nb_parties_gagnees' => 0,
            'nb_parties_perdues' => 0,
            'prix' => 0,
            'salt' => null,
            'pourcentage_utilisation' => 0,
            'pourcentage_cartes_bleues' => 0,
            'pourcentage_cartes_jaunes' => 0,
            'pourcentage_cartes_rouges' => 0,
            'pourcentage_cartes_noires' => 0,
            'pourcentage_cartes_vertes' => 0,
            'pourcentage_cartes_blanches' => 0
        ]);

        return response()->json([
            'data' => $deckNonModifie
        ]);
    }
}
