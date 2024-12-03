<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ami extends Model
{
    use HasFactory;

    protected $table = "amis";

    protected $fillable = [
        'utilisateur_demandeur_id',
        'utilisateur_receveur_id',
        'invitation_accepter',
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
     * * Relation avec l'utilisateur demandeur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilisateurDemandeur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_demandeur_id');
    }

    /**
     * * Relation avec l'utilisateur receveur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilisateurReceveur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_receveur_id');
    }
}
