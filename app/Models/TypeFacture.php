<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeFacture extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];
    public function facture()
    {
        return $this->hasMany(Facture::class);
    }

}
