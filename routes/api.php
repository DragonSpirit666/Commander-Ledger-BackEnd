<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('utilisateurs')
    ->controller(ProfileController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', 'indexUtilisateur');
    });

Route::middleware(['api'])->group(function () {
    require __DIR__ . '/auth.php';
});
