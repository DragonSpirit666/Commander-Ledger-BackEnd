<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'partie' => [
                'id' => $this['id'],
                'date' => $this['date'],
                'nb_participants' => $this['nb_participants'],
                'terminee' => $this['terminee'],
                'createur' => $this['createur_id'],
                'gagnant' => $this['gagnant_id'],
            ],
            'utilisateurs_decks' => new UtilisateurDeckCollection($this['participants'])
        ];
    }
}
