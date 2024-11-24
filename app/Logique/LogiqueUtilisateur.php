<?php

use App\Models\Deck;
use App\Models\Utilisateur;

/**
 * Fonction pour calculer le prix total des decks d'un utilisateur
 *
 * @return void
 */
function CalculerPrixTotalsDecksUtilisateur(Utilisateur $utilisateur): void
{
    $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();
    $prixTotal = 0;

    foreach ($decks as $deck) {
        $prixTotal += $deck->prix;
    }

    $utilisateur->update(['prixTotal' => $prixTotal]);
}
