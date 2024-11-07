<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'une partie
 */
class Partie extends Model
{
    protected $fillable = [
        'termine',
        'gagnant_id',
    ];

    // TODO import Utilisateur quand pulled
    /**
     * Trouve le gagnant de la partie
     *
     * @return BelongsTo un utilisateur
     */
    protected function gagnant() : BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'gagnant_id');
    }

    // TODO import Utilisateur quand pulled
    /**
     * Trouve le créateur de la partie
     *
     * @return BelongsTo un utilisateur
     */
    protected function createur() : BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'createur_id');
    }
}
