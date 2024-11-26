<?php
namespace App\Http\Logique;

use App\Models\Deck;
use App\Http\Logique\APIExterne;
use App\Http\Logique\CompterCouleur;
class CreationDeck
{
    public static function creerDeck(array $data, $id) : Deck {
        $data['utilisateur_id'] = (int)$id;
        $lignes = explode("\n", $data["cartes"]);
        $cartes = array();


        foreach ($lignes as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // validation des arguments
            if (!preg_match('/^(\d+)\s+(.*)$/', $line, $matches)) {
                throw new \InvalidArgumentException("Invalid card format: '{$line}'. Expected format: '<quantity> <card name>'.");
            }

            $quantity = intval($matches[1]);
            $cardName = $matches[2];
            $cartes[$cardName] = $quantity;
        }

        // validation de la présence de carte
        if (empty($cartes)) {
            throw new \InvalidArgumentException('Le paramètre cartes doit contenir au moins une carte valide.');
        }

        $deck = Deck::create($data);
        $deck->utilisateur()->associate($id);

        $cartesDetails = array();
        foreach ($cartes as $cardName => $quantity) {
            $apiResponse = APIExterne::AppelleAPICartes($cardName);
            // Decode the JSON response into an array
            $decodedResponse = json_decode($apiResponse, true);

            // A TESTER AVEC LES ERREUR ENVOYER PAR SCRYFALL
            if (empty($decodedResponse) || !isset($decodedResponse['prices']['usd'])) {
                throw new \RuntimeException("Failed to fetch details for card: '{$cardName}'.");
            }

            $cartesDetails[] = [
                'carte_nom' => $cardName,
                'quantité' => $quantity,
                'couleurs' => $decodedResponse['colors'],
                'prix' => $decodedResponse['prices']["usd"]
            ];
        }

        $tauxCouleurs = json_decode(CompterCouleur::Compte($cartesDetails));

        foreach ($tauxCouleurs as $couleur => $prc) {
            switch ($couleur) {
                case "Blaqnches":
                    $deck->pourcentage_cartes_blanches = $prc;
                    break;
                case "Bleus":
                    $deck->pourcentage_cartes_bleues = $prc;
                    break;
                case "SansCouleur":
                    $deck->pourcentage_cartes_sans_couleur = $prc;
                    break;
                case "Rouges":
                    $deck->pourcentage_cartes_rouges = $prc;
                    break;
                case "Noirs":
                    $deck->pourcentage_cartes_noires = $prc;
                    break;
                case "Vertes":
                    $deck->pourcentage_cartes_vertes = $prc;
                    break;
            }
        }
        if (isset($data['prix'])) {
            $deck->prix = $data['prix'];
        } else {
            $prixTotal = 0;
            foreach ($cartesDetails as $carte) {
                $prixTotal += $carte['prix'];
            }

            $deck->prix = $prixTotal;
        }


        $deck->save();

        return $deck;
    }
}
