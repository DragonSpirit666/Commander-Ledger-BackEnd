<?php

use Illuminate\Database\Eloquent\Casts\Json;

function CompterCouleur(array $cartesDetails) : Json
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
        foreach ($cartes->colors as $color) {
            switch ($color) {
                case "U":
                    $statsCouleur["nbBleus"]++;
                    break;

                case "B":
                    $statsCouleur["nbNoirs"]++;
                    break;

                case "R":
                    $statsCouleur["nbRouges"]++;
                    break;

                case "G":
                    $statsCouleur["nbVertes"]++;
                    break;

                case "W":
                    $statsCouleur["nbBlaqnches"]++;
                    break;

                default:
                    $statsCouleur["nbSansCouleur"]++;
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
