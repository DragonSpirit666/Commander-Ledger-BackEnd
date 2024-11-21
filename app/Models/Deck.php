<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Définie les champs possible pour le model d'un deck.
 */
class Deck extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'photo',
        'cartes',
        'nb_parties_gagnees',
        'nb_parties_perdues',
        'prix',
        'salt',
        'pourcentage_utilisation',
        'supprime',
        'utilisateur_id',
        'pourcentage_cartes_bleues',
        'pourcentage_cartes_jaunes',
        'pourcentage_cartes_rouges',
        'pourcentage_cartes_noires',
        'pourcentage_cartes_vertes',
        'pourcentage_cartes_blanches'
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Définie l'utilisateur qui possède le deck.
     * @return BelongsTo l'utilisateur qui possède le deck.
     */
    public function utilisateur() : BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
