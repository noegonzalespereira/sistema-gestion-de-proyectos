<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador General',
            'email' => 'admin@institucion.edu',
            'password' => Hash::make('Admin123*'),
            'rol' => 'Administrador',
            'activo' => true,
        ]);
    }
}
