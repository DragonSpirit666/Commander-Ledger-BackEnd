<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Database\Factories\UtilisateurFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    /** @use HasFactory<UtilisateurFactory> */
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'courriel',
        'password',
        'photo',
        'prive',
        'nb_parties_gagnees',
        'nb_parties_perdues',
        'prix_total_decks'
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtenez les attributs qui doivent être convertis.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'courriel_verified_at' => 'datetime',
            'password' => 'hashed',
            'prive' => 'boolean',
            'nb_parties_gagnees' => 'integer',
            'nb_parties_perdues' => 'integer',
            'prix_total_decks' => 'decimal:2',
        ];
    }

    public function EnvoiDemandeAmi()
    {
        return $this->hasMany(Ami::class, 'user_1_id');
    }

    public function RecevoirDemandeAmi()
    {
        return $this->hasMany(Ami::class, 'user_2_id');
    }

    public function amisAccepter()
    {
        return $this->friendsSent()->where('invitation_accepter', true)
            ->orWhereHas('RecevoirDemandeAmi', function ($query) {
                $query->where('invitation_accepter', true);
            });
    }
}
