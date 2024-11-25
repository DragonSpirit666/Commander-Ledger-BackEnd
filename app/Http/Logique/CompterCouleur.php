<?php
namespace App\Http\Logique;

use Illuminate\Database\Eloquent\Casts\Json;

class CompterCouleur {
    /**
     * Compte les cartes par couleur et renvoie les pourcentages de chaque couleur.
     *
     * @param array $cartesDetails Un tableau contenant les détails des cartes, y compris les couleurs et les quantités.
     * @return string Renvoie une string JSON contenant les pourcentages de chaque couleur.
     */
    public static function Compte(array $cartesDetails) : string
    {
        $statsCouleur = [
            "nbBlaqnches" => 0,
            "nbBleus" => 0,
            "nbSansCouleur" => 0,
            "nbRouges" => 0,
            "nbNoirs" => 0,
            "nbVertes" => 0,
        ];

        foreach ($cartesDetails as $cartes) {
            foreach ($cartes["couleurs"] as $color) {
                switch ($color) {
                    case "U":
                        $statsCouleur["nbBleus"] += $cartes["quantité"];
                        break;

                    case "B":
                        $statsCouleur["nbNoirs"] += $cartes["quantité"];
                        break;

                    case "R":
                        $statsCouleur["nbRouges"] += $cartes["quantité"];
                        break;

                    case "G":
                        $statsCouleur["nbVertes"] += $cartes["quantité"];
                        break;

                    case "W":
                        $statsCouleur["nbBlaqnches"] += $cartes["quantité"];
                        break;

                    default:
                        $statsCouleur["nbSansCouleur"] += $cartes["quantité"];
                        break;
                }
            }
        }

        $totalCartes = 0;

        foreach ($statsCouleur as $stat => $nb) {
            $totalCartes += $nb;
        }

        $pourcentageCouleurs = [];

        foreach ($statsCouleur as $stat => $nb) {
            $pourcentageCouleurs[substr($stat, 2)] = round($nb / $totalCartes, 2) * 100;
        }

        return Json::encode($pourcentageCouleurs);
    }
}

