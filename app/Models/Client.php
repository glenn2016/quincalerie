<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable= [  
        'nom',
        'prenom',
        'nom_entreprise',
        'email',
        'telephome_un',
        'telephome_deux',
        'adresse',
        'usercreate',
    ];
}
