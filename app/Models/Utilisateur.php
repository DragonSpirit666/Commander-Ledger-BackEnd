<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'couriel',
        'mot_de_passe',
        'photo',
        'prive',
        'nb_parties_gagnees',
        'nb_parties_perdues',
        'prix_total_decks'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'couriel_verified_at' => 'datetime',
            'mot_de_passe' => 'hashed',
            'prive' => 'boolean',
            'nb_parties_gagnees' => 'integer',
            'nb_parties_perdues' => 'integer',
            'prix_total_decks' => 'decimal:2',
        ];
    }
}
