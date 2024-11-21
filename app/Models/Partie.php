<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'une partie
 */
class Partie extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'nb_participants',
        'terminee',
        'gagnant_id',
        'createur_id'
    ];

    /**
     * Trouve le gagnant de la partie
     *
     * @return BelongsTo un utilisateur
     */
    public function gagnant() : BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'gagnant_id');
    }

    /**
     * Trouve le créateur de la partie
     *
     * @return BelongsTo un utilisateur
     */
    public function createur() : BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'createur_id');
    }
}
