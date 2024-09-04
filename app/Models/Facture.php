<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'client_id',
        'type_facture_id',
        'total',
        'date',
        'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,);
    }
    public function client()
    {
        return $this->belongsTo(Client::class,);
    }
    public function typefacture()
    {
        return $this->belongsTo(TypeFacture::class,);
    }
    public function produits()
    {
        return $this->belongsToMany(Facture::class);
    }
}
