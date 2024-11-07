<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'une partie-deck
 */
class PartieDeck extends Model
{
    protected $fillable = [
        'valide',
        'position',
    ];

    /**
     * Trouve la partie associée
     *
     * @return BelongsTo une partie
     */
    protected function partie() : BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }

    // TODO import Deck quand pulled
    /**
     * Trouve le deck associé
     *
     * @return BelongsTo un deck
     */
    protected function deck() : BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }
}
