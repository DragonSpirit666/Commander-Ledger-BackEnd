<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjoutDeckRequest;
use App\Http\Requests\PartieRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PartieCollection;
use App\Http\Resources\PartieResource;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Logique\CreationDeck;
use App\Logique\LogiqueUtilisateur;
use App\Models\Deck;
use App\Models\Partie;
use App\Models\PartieDeck;
use App\Models\Ami;

use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
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
        $utilisateurs = Utilisateur::where('supprime', false)->get();

        foreach ($utilisateurs as $utilisateur) {
            LogiqueUtilisateur::CalculerPrixTotalsDecksUtilisateur($utilisateur);
            LogiqueUtilisateur::CalculerRatioPartiesGagneesUtilisateur($utilisateur);
        }

        return response()->json(new UtilisateurCollection($utilisateurs));
    }

    /**
     * Renvoyer un utilisateur
     *
     * @param $id string
     * @return JsonResponse
     */
    public function showUtilisateur(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);

        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        LogiqueUtilisateur::CalculerPrixTotalsDecksUtilisateur($utilisateur);
        LogiqueUtilisateur::CalculerRatioPartiesGagneesUtilisateur($utilisateur);

        return response()->json([
            'data' => new UtilisateurResource($utilisateur),
        ]);
    }

    /**
     * Modification d'un utilisateur
     *
     * @param Request $requete
     * @param $id string
     * @return JsonResponse
     */
    public function updateUtilisateur(Request $requete, string $id): JsonResponse
    {
        $donneesValide = $requete->validate([
            'nom' => 'required|string|unique:utilisateurs,nom,' . $id,
            'courriel' => 'required|string|email',
            'photo' => 'nullable|string|max:255',
            'prive' => 'required|boolean',
        ]);

        $utilisateur = Utilisateur::find($id);

        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $donneesValide['prive'] = filter_var($donneesValide['prive'], FILTER_VALIDATE_BOOLEAN);

        $utilisateur->update($donneesValide);

        LogiqueUtilisateur::CalculerPrixTotalsDecksUtilisateur($utilisateur);
        LogiqueUtilisateur::CalculerRatioPartiesGagneesUtilisateur($utilisateur);

        return response()->json([
            'data' => new UtilisateurResource($utilisateur),
        ]);
    }

    /**
     * Supression d'un utilisateur
     *
     * @param $id string
     * @return JsonResponse
     */
    public function destroyUtilisateur(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);

        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        LogiqueUtilisateur::CalculerPrixTotalsDecksUtilisateur($utilisateur);
        LogiqueUtilisateur::CalculerRatioPartiesGagneesUtilisateur($utilisateur);

        $utilisateurNonModifie = $utilisateur->replicate();
        $utilisateurNonModifie->id = $id;
        $utilisateurNonModifie->supprime = true;

        $utilisateur->update([
            'nom' => 'Inconnu' . $utilisateur->id,
            'courriel' => 'inconnu' . $utilisateur->id . '@example.com',
            'photo' => null,
            'prive' => false,
            'password' => bcrypt(Str::random()),
            'supprime' => true
        ]);

        return response()->json([
            'data' => new UtilisateurResource($utilisateurNonModifie),
        ]);
    }

    /**
     * Acceptation d'amitié
     *
     * @param $id string Id actuel, du receveur
     * @param $id_ami string Id du demandeur
     * @param Request $requete Obtient le bool avec la réponse pour la demande d'amitié
     * @return JsonResponse
     */
    public function acceptationAmi(string $id, string $id_ami, Request $requete): JsonResponse
    {
        // TODO valider que la demande a pas deja ete accepter
        $requete->validate(['invitation_acceptee' => ['required', 'boolean']]);

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $ami = Ami::find($id_ami);

        if (!$ami) {
            return response()->json(['message' => 'Demande d\'ami n\'est pas trouvé.'], 404);
        }

        if ($ami->utilisateur_receveur_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé à accepter ou refuser cette demande.'], 403);
        }

        if ($requete->invitation_acceptee) {
            // Accepter la demande
            $ami->invitation_accepter = true;
            $ami->save();
            return response()->json(['data' => ['message' => 'Demande d\'ami acceptée.']], 200);
        } else {
            // Refuser la demande
            $ami->delete();
            return response()->json(['data' => ['message' => 'Demande d\'ami rejetée.']], 200);
        }
    }

    /**
     * Envoyer une demande d'ami
     *
     * @param $id string Id du demandeur
     * @param $id_ami string Id du receveur
     * @return JsonResponse
     */
    public function envoyerDemandeAmi(string $id, Request $requete): JsonResponse
    {
        $requete->validate(['utilisateur_receveur_id' => ['required', 'int', 'exists:utilisateurs,id']]);
        $id_ami = $requete->utilisateur_receveur_id;

        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $utilisateurAmi = Utilisateur::find($id_ami);
        if ($utilisateurAmi === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id_ami.'\' n\'existe pas'], 404);
        }

        if ($id == $id_ami) {
            return response()->json(['message' => 'Tu ne peux pas envoyer une demande d\'ami à toi-même.'], 400);
        }

        // Vérification si l'amitié existe <3
        $demandeExistante = Ami::where(function ($query) use ($id, $id_ami) {
            $query->where('utilisateur_demandeur_id', $id)
                ->where('utilisateur_receveur_id', $id_ami);
        })
            ->orWhere(function ($query) use ($id, $id_ami) {
                $query->where('utilisateur_demandeur_id', $id_ami)
                    ->where('utilisateur_receveur_id', $id);
            })
            ->first();

        if ($demandeExistante) {
            return response()->json(['message' => 'Une demande d\'ami existe déjà entre pour ces deux utilisateurs.'], 400);
        }

        Ami::create([
            'utilisateur_demandeur_id' => $id,
            'utilisateur_receveur_id' => $id_ami,
            'invitation_accepter' => false,
        ]);

        return response()->json(['data' => ['message' => 'Demande d\'ami envoyer avec succès.']], 201);
    }

    /**
     * Récupérer la liste des amis d'un utilisateur.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function obtenirListeAmis(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $amis = $utilisateur->amisAccepter();

        return response()->json(['data' => $amis], 200);
    }


    /**
     * Obtenir liste de demande obtenues en attente d'acceptation
     *
     * @param $id string
     * @return JsonResponse
     */
    public function notificationDemandeAmi(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $requete = Ami::where('utilisateur_receveur_id', $id)
        ->where('invitation_accepter', false)
        ->get();

        return response()->json(['data' => $requete]);
    }

    /**
     * FONCTION NON UTLISÉ, ROUTE EFFACER
     *
     * Obtenir liste des acceptations demande d'amis en attente
     * @param $id string
     * @return JsonResponse
     */
    public function obtenirAcceptationAmiEnAttente(string $id): JsonResponse
    {
        $requete = Ami::where('utilisateur_demandeur_id', $id)
        ->where('invitation_accepter', false)
        ->get();

        return response()->json($requete);
    }

    /**
     * Refuser une demande d'ami
     *
     * @param $id string
     * @param $id_ami string
     * @return JsonResponse
     */
    public function EffacerAmitie(string $id, string $id_ami)
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $ami = Ami::where('utilisateur_demandeur_id', $id)
            ->where('utilisateur_receveur_id', $id_ami)
            ->first();

        if (!$ami) {
            return response()->json(['message' => 'Demande d\'ami non trouvée ou déjà rejetée.'], 404);
        }

        $ami->delete();

        return response()->json(['data' => ['message' => 'Amitié détruit avec succès.', 'ami' => $ami]]);
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
     * Ajout d'un deck manuel
     *
     * @param AjoutDeckRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function ajouterDeckManuel(AjoutDeckRequest $request, $id) : JsonResponse
    {
        $data = $request->validated();
        $data['utilisateur_id'] = (int)$id;

        try {
            $deck = CreationDeck::creerDeck($data, $id);
        } catch (\Exception $error) {
            return response()->json([$error->getMessage()], 422);
        }

        return response()->json([$deck], 201);
    }

    /**
     * Récupère les decks d'un utilisateur
     *
     * @param $id string Id de l'utilisateur
     * @return JsonResponse Les decks de l'utilisateur
     */
    public function indexDeck(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();

        foreach ($decks as $deck) {
            $deck->nb_parties_gagnees = $this->CalculerDeckGagnees($deck->id);
            $deck->nb_parties_perdues = $this->CalculerDeckPerdu($deck->id);
            $deck->pourcentage_utilisation = $this->CalculerDeckPourcentageUtilisation($deck->id);
        }

        return response()->json([
            'data' => $decks,
        ]);
    }

    /**
     * Récupère un deck d'un utilisateur
     *
     * @param $id string Id de l'utilisateur
     * @param $deckId string Id du deck
     * @return JsonResponse Le deck de l'utilisateur
     */
    public function showDeck(string $id, string $deckId): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $deck = Deck::find($deckId);
        if ($deck === null) {
            return response()->json(['message' => 'Le deck \''.$deckId.'\' n\'existe pas'], 404);
        }

        $deck->nb_parties_gagnees = $this->CalculerDeckGagnees($deck->id);
        $deck->nb_parties_perdues = $this->CalculerDeckPerdu($deck->id);
        $deck->pourcentage_utilisation = $this->CalculerDeckPourcentageUtilisation($deck->id);

        return response()->json(['data' => $deck]);
    }

    /**
     * Calculer le nombre de parties gagnées par un deck
     *
     * @param $deckId int Id du deck
     * @return int Nombre de parties gagnées
     */
    private function CalculerDeckGagnees(int $deckId) : int {
        $partiesDeck = PartieDeck::where('deck_id', $deckId)->
        where('position', 1)->where('validee', True)->
        where('refusee', False)->get();

        return count($partiesDeck);
    }

    /**
     * Calculer le nombre de parties perdues par un deck
     *
     * @param $deckId int Id du deck
     * @return int Nombre de parties perdues
     */
    private function CalculerDeckPerdu(int $deckId) : int {
        $partiesDeck = PartieDeck::where('deck_id', $deckId)->
        where('position', '>', 1)->where('validee', True)->
        where('refusee', False)->get();

        return count($partiesDeck);
    }

    /**
     * Calculer le pourcentage d'utilisation d'un deck
     *
     * @param $deckId int Id du deck
     * @return int Pourcentage d'utilisation
     */
    private function CalculerDeckPourcentageUtilisation(int $deckId) : int {
        $partiesDeck = count(PartieDeck::where('deck_id', $deckId)->where('validee', True)->
        where('refusee', False)->get());
        $partiesDeckTotal = 0;

        $utilisateur = Deck::find($deckId)->utilisateur;
        $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();

        foreach ($decks as $deck) {
            $partiesDeckTotal += count(PartieDeck::where('deck_id', $deck->id)->where('validee', True)->
            where('refusee', False)->get());
        }

        if ($partiesDeckTotal == 0) {
            return 0;
        }

        return (int)round(($partiesDeck * 100.0) / ($partiesDeckTotal));
    }

    /**
     * Création d'une partie
     *
     * @param string $id Id de l'utilisateur qui créer la partie
     * @param PartieRequest $request Request avec les informations envoyées
     *
     * @return JsonResponse Information sur la partie créée
     */
    public function storePartie(string $id, PartieRequest $request) : JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $request->validated();

        $decksEntres = [];
        $positionsEntres = [];

        $listeParticipants = $request->get('participants');
        $nbParticippants = count($listeParticipants);

        // Ne peut pas vérifier les doublons avec des requests validation
        foreach ($listeParticipants as $participant) {
            if (in_array($participant['deck_id'], $decksEntres)) {
                return response()->json(['message' => 'Le deck \''.$participant['deck_id'].'\' ne peut participer en double dans la partie.'], 422);
            }

            $decksEntres[] = $participant['deck_id'];

            if (in_array($participant['position'], $positionsEntres)) {
                return response()->json(['message' => 'Deux participants ne peuvent avoir terminé à la position '.$participant['position'].'.'], 422);
            }

            if ($participant['position'] < 1 || $participant['position'] >$nbParticippants) {
                return response()->json(['message' => 'La position des participants doit se trouver entre 1 et le nombre de participants ('.$nbParticippants.').'], 422);
            }

            $positionsEntres[] = $participant['position'];
        }

        $partie = Partie::create([
            'date' => $request->get('date'),
            'nb_participants' => $nbParticippants,
            'terminee' => true,
            'createur_id' => $id,
        ]);

        $listePartiesDecks = [];

        foreach ($listeParticipants as $participant) {
            $deck = Deck::find($participant['deck_id']);

            $partieDeck = PartieDeck::create([
                'partie_id' => $partie->id,
                'deck_id' => $participant['deck_id'],
                'position' => $participant['position'],
                'validee' => $deck->utilisateur->id == $id
            ]);

            $listePartiesDecks[] = $partieDeck;

            if ($partieDeck->position == 1) {
                $partie->update(['gagnant_id' => $deck->utilisateur->id]);
            }
        }

        return response()->json(['data' =>
            new PartieResource([
                'id' => $partie->id,
                'date' => $partie->date,
                'nb_participants' => $nbParticippants,
                'terminee' => $partie->terminee,
                'createur_id' => $id,
                'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
                'participants' => $listePartiesDecks,
            ])
        ]);
    }

    /**
     * Récupère toutes les parties associées à un utilisateur
     *
     * @param string $id Id de l'utilisateur dont on veut les parties
     *
     * @return JsonResponse Toutes les parties auquelles l'utilisateur est associé
     */
    public function indexPartie(string $id): JsonResponse
    {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $decks = Deck::where('utilisateur_id', $id);
        $partiesDecksUtilisateur = PartieDeck::whereIn('deck_id', $decks->pluck('id'))->where('validee', true)->where('refusee', false)->get();
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

        return response()->json([
            'data' => new PartieCollection($information)
        ]);
    }

    /**
     * Récupère une partie
     *
     * @param string $id Id de l'utilisateur qui a fait la requête
     * @param int $partieId Id de la partie à récupérer
     *
     * @return PartieResource Partie trouvée
     */
    public function showPartie(string $id, int $partieId): JsonResponse {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $partie = Partie::find($partieId);
        if ($partie === null) {
            return response()->json(['message' => 'La partie \''.$partieId.'\' n\'existe pas'], 404);
        }

        $partiesDecks = PartieDeck::where('partie_id', $partieId)->get();

        return response()->json([
            'data' => new PartieResource([
                'id' => $partie->id,
                'date' => $partie->date,
                'nb_participants' => $partie->nb_participants,
                'terminee' => $partie->terminee,
                'createur_id' => $partie->createur->id,
                'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
                'participants' => $partiesDecks,
            ])
        ]);
    }

    /**
     * Récupère les parties associées à un utilisateur qui n'ont pas encore été acceptées
     *
     * @param string $id id de l'utilisateur
     *
     * @return PartieCollection la liste des parties pas encore acceptée / refusée
     */
    public function notificationInvitationPartie(string $id): JsonResponse {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        // TODO verification que le user qui repond est celui auth
        $decks = Deck::where('utilisateur_id', $id)->get();

        $invitationsParties = PartieDeck::whereIn('deck_id', $decks->pluck('id'))->where('validee', false)->get();
        $parties = Partie::wherein('id', $invitationsParties->pluck('partie_id'))->get();

        $information = [];

        foreach ($parties as $partie) {
            $partieDecks = PartieDeck::where('partie_id', $partie->id)->get();

            $information[] = [
                'id' => $partie->id,
                'date' => $partie->date,
                'nb_participants' => $partie->nb_participants,
                'terminee' => $partie->terminee,
                'createur_id' => $partie->createur->id,
                'gagnant_id' => $partie->gagnant ? $partie->gagnant->id : null,
                'participants' => $partieDecks,
            ];
        }

        return response()->json([
            'data' => new PartieCollection($information)
        ]);
    }

    /**
     * Update l'invitation à une partie avec la réponse (acceptée ou non) et update les statistiques utilisateurs et decks
     * nécessaire selon la réponse.
     *
     * @param string $id id de l'utilisateur qui reçoit l'invitation
     * @param string $invitationId id de l'invitation (PartieDeck)
     * @param Request $request request contenant la réponse (acceptee)
     *
     * @return JsonResponse
     */
    public function acceptationInvitationPartie(string $id, string $invitationId, Request $request): JsonResponse {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        // TODO valider que le user a qui l'invitation est envoyer est celui qui est login
        $request->validate(['invitation_acceptee' =>  ['required', 'boolean']]);

        $partieDeck = PartieDeck::findorfail($invitationId);
        if ($partieDeck->validee) {
            return response()->json(['message' => 'Cette invitation n\'existe pas.'], 404);
        }

        Utilisateur::findorfail($id);

        if ($request->invitation_acceptee == 1) {
            $partieDeck->update(['validee' => true, 'refusee' => false]);
            return response()->json(['message' => 'Invitation à la partie acceptée.']);
        } else {
            $partieDeck->update(['validee' => true, 'refusee' => true]);
            return response()->json(['message' => 'Invitation à la partie refusée.']);
        }
    }

    /**
     * Supprime un deck (l'anonymise)
     *
     * @param string $id id du deck à supprimer
     * @return JsonResponse information du deck avant son anonymisation
     */
    public function deleteDeck(string $id, string $deckId): JsonResponse {
        $utilisateur = Utilisateur::find($id);
        if ($utilisateur === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$id.'\' n\'existe pas'], 404);
        }

        $deck = Deck::find($deckId);
        if ($deck === null) {
            return response()->json(['message' => 'L\'utilisateur \''.$deckId.'\' n\'existe pas'], 404);
        }

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
