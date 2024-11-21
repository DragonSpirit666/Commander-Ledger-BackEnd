<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UtilisateurCollection;
use App\Http\Resources\UtilisateurResource;
use App\Models\Ami;
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
     * Acceptation d'amitié
     *
     * @param $id // id actuel, du receveur
     * @param $id_ami // id du demandeur
     * @return JsonResponse
     */
    public function acceptationAmi($id, $id_ami)
    {
        $user_2_id = $id;

        $ami = Ami::where('user_1_id', $id_ami)
                    ->where('user_2_id', $user_2_id)
                    ->first();

        if (!$ami) {
            return response()->json(['message' => 'Demande d\'ami n\'est pas trouvé.'], 404);
        }

        if ($ami->user_2_id != auth()->id()) {
            return response()->json(['message' => 'N\'est pas autorisé à accepter cette requête.'], 403);
        }

        $ami->invitation_accepter = true;
        $ami->save();

        return response()->json(['message' => 'Demande d\'ami accepté.'], 200);
    }

    /**
     * Envoyer une demande d'ami
     *
     * @param $id // id du demandeur
     * @param $id_ami // id du receveur
     * @return JsonResponse
     */
    public function envoyerDemandeAmi($id, $id_ami)
    {
        if ($id === $id_ami) {
            return response()->json(['message' => 'Tu ne peux pas envoyer une demande d\'ami à toi-même.'], 400);
        }

        // Vérification si l'amitié existe <3
        $demandeExistante = Ami::where(function ($query) use ($id, $id_ami) {
            $query->where('user_1_id', $id)
                ->where('user_2_id', $id_ami);
        })
            ->orWhere(function ($query) use ($id, $id_ami) {
                $query->where('user_1_id', $id_ami)
                    ->where('user_2_id', $id);
            })
            ->first();

        if ($demandeExistante) {
            return response()->json(['message' => 'Une demande d\'ami existe déjà entre pour ces deux utilisateurs.'], 400);
        }

        Ami::create([
            'user_1_id' => $id, // Le demandeur
            'user_2_id' => $id_ami, // Le receveur
            'invitation_accepter' => false,
        ]);

        return response()->json(['message' => 'Demande d\'ami envoyer avec succès.'], 201);
    }

    /**
     * Obtenir liste de demande envoyer en attente d'acceptation
     *
     * @param $id
     * @return JsonResponse
     */
    public function obtenirDemandeAmiEnAttente($id)
    {
        $requete = Ami::where('user_1_id', $id)
        ->where('invitation_accepter', false)
        ->get();

        return response()->json([$requete]);
    }

    /**
     * Obtenir liste des acceptations demande d'amis en attente
     * @param $id
     * @return JsonResponse
     */
    public function obtenirAcceptationAmiEnAttente($id)
    {
        $requete = Ami::where('user_2_id', $id)
        ->where('invitation_accepter', false)
        ->get();

        return response()->json($requete);
    }

    /**
     * Refuser une demande d'ami
     *
     * @param $id
     * @param $id_ami
     * @return JsonResponse
     */
    public function EffacerDemandeOuAmitie($id, $id_ami)
    {
        $ami = Ami::where('user_1_id', $id)
            ->where('user_2_id', $id_ami)
            ->first();

        if (!$ami) {
            return response()->json(['message' => 'Demande d\'ami non trouvée ou déjà rejetée.'], 404);
        }

        $ami->delete();

        return response()->json(['message' => 'Amitié détruit avec succès.']);
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
}
