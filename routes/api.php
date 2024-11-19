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
        // Route pour ajouter une partie
        Route::post('/{id}/parties', [ProfileController::class, 'storePartie']);
        // Route pour get toutes les parties d'un utilisateur
        Route::get('/{id}/parties', [ProfileController::class, 'indexPartie']);
});

Route::middleware(['api'])->group(function () {
    require __DIR__ . '/auth.php';
});

