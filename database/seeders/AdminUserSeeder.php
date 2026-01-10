<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::create([
            'name' => 'Super',
            'firstname' => 'Admin',
            'email' => 'admin@housework.com',
            'password' => Hash::make('password123'), // Mot de passe sécurisé
            'phone' => '0000000000',
            'role' => 'admin', // C'est le boss
        ]);
        
        // la ménagère pour tester l'assignation plus tard
        User::create([
            'name' => 'Doe',
            'firstname' => 'Jane',
            'email' => 'jane@housework.com',
            'password' => Hash::make('password123'),
            'phone' => '0987654321',
            'role' => 'houseworker',
        ]);
    }
}
