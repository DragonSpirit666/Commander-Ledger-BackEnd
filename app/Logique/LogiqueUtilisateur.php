<?php
namespace App\Logique;

use App\Models\Deck;
use App\Models\Utilisateur;

/**
 * Classe pour regrouper les fonctions pour la logique liée aux utilisateurs
 */
class LogiqueUtilisateur {

    /**
     * Fonction pour calculer le prix total des decks d'un utilisateur et update le champ prix_total_decks pour celui-ci
     *
     * @return void
     */
    public static function CalculerPrixTotalsDecksUtilisateur(Utilisateur $utilisateur): void {
        $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();
        $prixTotal = 0;

        foreach ($decks as $deck) {
            $prixTotal += $deck->prix;
        }

        $utilisateur->update(['prix_total_decks' => $prixTotal]);
    }
}
