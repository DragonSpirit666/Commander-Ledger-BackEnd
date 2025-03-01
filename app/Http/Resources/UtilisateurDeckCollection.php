<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection des decks d<un utilisateur pour les parties
 */
class UtilisateurDeckCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($utilisateurDeck) {
            return [
                'invitation_id' => $utilisateurDeck->id,
                'utilisateur' => [
                    'id' => $utilisateurDeck->deck->utilisateur->id,
                    'nom' => $utilisateurDeck->deck->utilisateur->nom,
                    'photo' => $utilisateurDeck->deck->utilisateur->photo,
                ],
                'deck' => [
                    'id' => $utilisateurDeck['deck_id'],
                    'nom' => $utilisateurDeck->deck->nom,
                    'photo' => $utilisateurDeck->deck->photo,
                ],
                'position' => $utilisateurDeck['position'],
            ];
        })->all();
    }
}
