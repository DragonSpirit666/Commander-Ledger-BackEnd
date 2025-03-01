<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('utilisateurs')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        // Route pour la liste des utilisateurs
        Route::get('/', [ProfileController::class, 'indexUtilisateur']);
        // Route pour obtenir un utilisateur
        Route::get('/{id}', [ProfileController::class, 'showUtilisateur']);
        // Route pour la modification utilisateur
        Route::put('/{id}', [ProfileController::class, 'updateUtilisateur']);
        // Route pour supprimer un utilisateur
        Route::delete('/{id}', [ProfileController::class, 'destroyUtilisateur']);

        // Route pour accepter demande d'amis
        Route::put('/{id}/amis/demandes/{id_ami}', [ProfileController::class, 'acceptationAmi']);
        // Route pour créer une demande d'amis
        Route::post('/{id}/amis/demandes', [ProfileController::class, 'envoyerDemandeAmi']);
        // Route pour obtenir les demandes envoyées par un utilisateur
        Route::get('/{id}/amis/demandes', [ProfileController::class, 'notificationDemandeAmi']);
        // Route pour obtenir la liste des amis d'un utilisateur
        Route::get('/{id}/amis', [ProfileController::class, 'obtenirListeAmis']);
        // Effacer une demande ou une amitié existante
        Route::delete('/{id}/amis/{id_ami}', [ProfileController::class, 'EffacerAmitie']);

        // Route pour la liste des decks
        Route::get('/{id}/decks', [ProfileController::class, 'indexDeck']);
        // Route pour obtenir un deck
        Route::get('/{id}/decks/{deckId}', [ProfileController::class, 'showDeck']);
        // Route pour supprimer un deck
        Route::delete('/{id}/decks/{deckId}', [ProfileController::class, 'deleteDeck']);
        // Route pour l'ajout de deck manuel
        Route::post('/{id}/decks', [ProfileController::class, 'ajouterDeckManuel']);

        // Route pour ajouter une partie
        Route::post('/{id}/parties', [ProfileController::class, 'storePartie']);
        // Route pour get toutes les parties d'un utilisateur
        Route::get('/{id}/parties', [ProfileController::class, 'indexPartie']);
        // Route pour get les invitations à une partie
        Route::get("/{id}/parties/invitations", [ProfileController::class, 'notificationInvitationPartie']);
        // Route pour répondre à une invitation de partie
        Route::put("/{id}/parties/invitations/{invitationId}", [ProfileController::class, 'acceptationInvitationPartie']);
        // Route pour get une partie
        Route::get('/{id}/parties/{partieId}', [ProfileController::class, 'showPartie']);
    });


Route::middleware(['api'])->group(function () {
    require __DIR__ . '/auth.php';
});

