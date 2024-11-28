<?php
namespace App\Logique;

use App\Models\Deck;
use App\Models\PartieDeck;
use App\Models\Utilisateur;

/**
 * Classe pour regrouper les fonctions pour la logique liée aux utilisateurs
 */
class LogiqueUtilisateur {

    /**
     * Fonction pour calculer le prix total des decks d'un utilisateur et update le champ prix_total_decks pour celui-ci
     *
     * @param Utilisateur $utilisateur utilisateur dont ont veux calculer le prix total des decks
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

    /**
     * Fonction pour calculer et update le nombre de parties gagnées et le nombre de parties perdues par l'utilisateur
     *
     * @param Utilisateur $utilisateur utilisateur dont ont veux calculer le ratio de parties gagnées
     * @return void
     */
    public static function CalculerRatioPartiesGagneesUtilisateur(Utilisateur $utilisateur): void {
        $decks = Deck::where('utilisateur_id', $utilisateur->id)->get();
        $nbPartiesJouer = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->count();

        $nbPartiesGagnees = PartieDeck::wherein('deck_id', $decks->pluck('id'))
            ->where('validee', true)
            ->where('refusee', false)
            ->where('position', 1)
            ->count();

        $nbPartiesPerdues = $nbPartiesJouer - $nbPartiesGagnees;

        $utilisateur->update([
            'nb_parties_gagnees' => $nbPartiesGagnees,
            'nb_parties_perdues' => $nbPartiesPerdues,
        ]);
    }
}
