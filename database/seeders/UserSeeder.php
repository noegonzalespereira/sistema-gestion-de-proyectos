<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🧑‍💼 ADMINISTRADOR
        // User::create([
        //     'name' => 'Administrador General',
        //     'email' => 'admin@institucion.edu',
        //     'password' => Hash::make('Admin123*'),
        //     'rol' => 'Administrador',
        //     'activo' => true,
        // ]);

        // DOCENTE
        User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos@usfx.com',
            'password' => Hash::make('Docente123*'),
            'rol' => 'Docente',
            'activo' => true,
        ]);

        //  ESTUDIANTE
        User::create([
            'name' => 'María González',
            'email' => 'maria@usfx.com',
            'password' => Hash::make('Estudiante123*'),
            'rol' => 'Estudiante',
            'activo' => true,
        ]);
    }
}

