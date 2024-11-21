<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ami extends Model
{
    use Factory;

    protected $table = "amis";

    protected $fillable = [
        'user_1_id',
        'user_2_id',
        'invitation_accepter',
    ];

    public function utilisateur1()
    {
        return $this->belongsTo(Utilisateur::class, 'user_1_id');
    }

    public function utilisateur2()
    {
        return $this->belongsTo(Utilisateur::class, 'user_2_id');
    }
}
