<?php
namespace App\Http\Logique;

use Illuminate\Database\Eloquent\Casts\Json;
class APIExterne
{
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

