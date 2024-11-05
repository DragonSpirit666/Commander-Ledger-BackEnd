<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UtilisateurResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'couriel' => $this->couriel,
            'photo' => $this->photo,
            'prive' => $this->prive,
            "nb_parties_gagnees" => $this->nb_parties_gagnees,
            "nb_parties_perdues" => $this->nb_parties_perdues,
            "prix_total_decks" => $this->prix_total_decks,
        ];
    }
}
