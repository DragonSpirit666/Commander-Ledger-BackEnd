<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'supprime'
    ];
}
