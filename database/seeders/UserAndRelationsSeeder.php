<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Models\Tribunal;

class UserAndRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. OBTENER CARREAS (usando el ID si existe)
        $carreraIS_id = DB::table('carreras')->where('sigla', 'IS')->value('id_carrera');
        $carreraAE_id = DB::table('carreras')->where('sigla', 'AE')->value('id_carrera');
        
        // --- 2. CREACIÓN DE USUARIOS BASE (Tabla: users) ---
        
        // Administrador (para iniciar sesión)
        // User::create([
        //     'name' => 'Admin Global',
        //     'email' => 'admin@proyecto.com',
        //     'password' => Hash::make('password'),
        //     'rol' => 'Administrador',
        //     'activo' => true,
        // ]);

        // Tutor
        $tutor_user = User::create([
            'name' => 'Ing. Ramiro Calizaya',
            'email' => 'calizaya@docente.com',
            'password' => Hash::make('Docente123*'),
            'rol' => 'Docente',
            'activo' => true,
        ]);
        $tutor_user = User::create([
            'name' => 'Ing. giovanna yapu',
            'email' => 'yapu@docente.com',
            'password' => Hash::make('Docente123*'),
            'rol' => 'Docente',
            'activo' => true,
        ]);
        $tutor_user = User::create([
            'name' => 'Ing. manuel arancibia',
            'email' => 'arancibia@docente.com',
            'password' => Hash::make('Docente123*'),
            'rol' => 'Docente',
            'activo' => true,
        ]);
        
        // Estudiante
        $estudiante_user = User::create([
            'name' => 'Marcela Miranda',
            'email' => 'miranda@estudiante.com',
            'password' => Hash::make('Estudiante123*'),
            'rol' => 'Estudiante',
            'activo' => true,
        ]);

        $estudiante_user = User::create([
            'name' => 'Valentina Pereira',
            'email' => 'pereira@estudiante.com',
            'password' => Hash::make('Estudiante123*'),
            'rol' => 'Estudiante',
            'activo' => true,
        ]);
        $estudiante_user = User::create([
            'name' => 'Javier Minto',
            'email' => 'minto@estudiante.com',
            'password' => Hash::make('Estudiante123*'),
            'rol' => 'Estudiante',
            'activo' => true,
        ]);

        // // --- 3. CREACIÓN DE TUTORES (Tabla: tutores) ---
        // Tutor::create([
        //     'id_usuario' => $tutor_user->id,
        //     'item' => 'T-001',
        //     // NO INCLUIR created_at/updated_at si public $timestamps = false
        // ]);
        // Tutor::create([
        //     'id_usuario' => $tutor_user->id,
        //     'item' => 'T-002',
        //     // NO INCLUIR created_at/updated_at si public $timestamps = false
        // ]);

        // // --- 4. CREACIÓN DE ESTUDIANTES (Tabla: estudiantes) ---
        // Estudiante::create([
        //     'id_usuario' => $estudiante_user->id,
        //     'ci' => '12345678',
        //     'id_carrera' => $carreraIS_id, // Asignación de carrera
        //     // NO INCLUIR created_at/updated_at si public $timestamps = false
        // ]);
        // Estudiante::create([
        //     'id_usuario' => $estudiante_user->id,
        //     'ci' => '10380909',
        //     'id_carrera' => $carreraIS_id, // Asignación de carrera
        //     // NO INCLUIR created_at/updated_at si public $timestamps = false
        // ]);
        
        // // --- 5. CREACIÓN DE TRIBUNALES (Tabla: tribunal) ---
        
    }
}