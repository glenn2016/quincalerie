<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeFacture;

class TypeFactureSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        TypeFacture::create([
            'nom'=>'Vente',
        ]);
        TypeFacture::create([
            'nom'=>'Emprunt',
        ]);
    }
}
