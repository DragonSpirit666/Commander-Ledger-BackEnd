<?php
namespace App\Http\Logique;

use Illuminate\Database\Eloquent\Casts\Json;
class APIExterne
{
    /**
     * Appelle l'API Scryfall pour obtenir des informations sur une carte spécifique.
     *
     * @param string $nomCarte Le nom exact de la carte à rechercher.
     * @return bool|array|string Renvoie une string JSON en cas de succès, un tableau avec les détails de l'erreur en cas d'échec, ou false en cas d'erreur inconnue.
     */
    public static function AppelleAPICartes(string $nomCarte): bool|array|string
    {
        $url = 'https://api.scryfall.com/cards/named';

        $params = array(
            'exact' => $nomCarte,
        );

        $queryString = http_build_query($params);

        $urlWithParams = $url . '?' . $queryString;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $urlWithParams);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'User-Agent: API',
            'Accept: */*',
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $reponseErreur = [
                "message" => "Erreur de curl",
                "erreur" => curl_error($curl)
            ];

            return $reponseErreur;
        } else {
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode >= 200 && $httpCode < 300) {
                // Successful response
                curl_close($curl);
                return $response;
            } else {

                $reponseErreur = [
                    "message" => "Erreur API",
                    "status_code" => $httpCode,
                    "response" => $response
                ];

                curl_close($curl);
                return $reponseErreur;
            }
        }

    }
}

