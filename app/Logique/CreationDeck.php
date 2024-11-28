<?php
namespace App\Logique;

use App\Models\Deck;

class CreationDeck
{
    /**
     * Crée un deck à partir des données fournies et de l'ID utilisateur.
     *
     * @param array $data Les données du deck, comprenant le nom et les cartes.
     * @param int $id L'ID de l'utilisateur associé au deck.
     * @return Deck Le deck créé.
     * @throws \InvalidArgumentException Si les paramètres requis sont manquants ou invalides.
     * @throws \RuntimeException Si une erreur survient lors de la requête à l'API externe.
     */
    public static function creerDeck(array $data, $id) : Deck {
        if (empty($data['nom']) || empty($data['cartes'])) {
            throw new \InvalidArgumentException("les paramêtres sont obligatoires");
        }

        $data['utilisateur_id'] = (int)$id;
        $lignes = explode("\n", $data["cartes"]);
        $cartes = array();

        foreach ($lignes as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // validation des arguments
            if (!preg_match('/^(\d+)\s+(.*)$/', $line, $matches)) {
                throw new \InvalidArgumentException("format de cartes invalide");
            }

            $quantity = intval($matches[1]);
            $cardName = $matches[2];
            $cartes[$cardName] = $quantity;
        }

        // validation de la présence de carte
        if (empty($cartes)) {
            throw new \InvalidArgumentException('Le paramètre cartes doit contenir au moins une carte valide.');
        }

        $cartesDetails = array();
        foreach ($cartes as $cardName => $quantity) {
            $apiResponse = APIExterne::AppelleAPICartes($cardName);

            if (is_array($apiResponse)) {
                throw new \RuntimeException("Erreur lors de la requête API");
            }

            $decodedResponse = json_decode($apiResponse, true);

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
                    $data["pourcentage_cartes_blanches"] = $prc;
                    break;
                case "Bleus":
                    $data["pourcentage_cartes_bleues"] = $prc;
                    break;
                case "SansCouleur":
                    $data["pourcentage_cartes_sans_couleur"] = $prc;
                    break;
                case "Rouges":
                    $data["pourcentage_cartes_rouges"] = $prc;
                    break;
                case "Noirs":
                    $data["pourcentage_cartes_noires"] = $prc;
                    break;
                case "Vertes":
                    $data["pourcentage_cartes_vertes"] = $prc;
                    break;
            }
        }
        if (!isset($data['prix'])) {
            $prixTotal = 0;
            foreach ($cartesDetails as $carte) {
                $prixTotal += $carte['prix'];
            }

            $data["prix"] = $prixTotal;
        }

        dump($data);

        $deck = Deck::create($data);

        $deck->utilisateur()->associate($id);

        $deck->save();

        return $deck;
    }

}
