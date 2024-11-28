<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'une partie-deck
 */
class PartieDeck extends Model
{
    use HasFactory;
    protected $fillable = [
        'validee',
        'refusee',
        'position',
        'partie_id',
        'deck_id'
    ];

    protected $table = 'parties_decks';

    /**
     * Trouve la partie associée
     *
     * @return BelongsTo une partie
     */
    public function partie() : BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }

    /**
     * Trouve le deck associé
     *
     * @return BelongsTo un deck
     */
    public function deck() : BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }
}
