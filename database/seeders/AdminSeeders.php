<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Créez un rôle d'administrateur s'il n'existe pas
        $adminRole = Role::firstOrCreate(['nom' => 'Admin']);
    
        // Créez un utilisateur administrateur
        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('passer123'), // Assurez-vous de hacher le mot de passe
        ]);
            
        // Attachez le rôle d'administrateur à l'utilisateur administrateur
        $admin->roles()->attach($adminRole->id);
    }
}
