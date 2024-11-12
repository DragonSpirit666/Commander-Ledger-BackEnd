<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UtilisateurCollection extends ResourceCollection
{
    /**
     * Transforme la collection ressource en array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($utilisateur) {
                return [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'photo' => $utilisateur->photo,
                    'prive' => $utilisateur->prive,
                    "nb_parties_gagnees" => $utilisateur->nb_parties_gagnees,
                    "nb_parties_perdues" => $utilisateur->nb_parties_perdues,
                    "prix_total_decks" => $utilisateur->prix_total_decks,
                ];
            })->all(),
        ];
    }
}
