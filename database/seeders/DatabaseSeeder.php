<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Entidades base (sin FKs)
            InstitucionSeeder::class,
            ProgramaSeeder::class,
            
            // Entidades dependientes
            CarreraSeeder::class, // Depende de Institucion
            UserAndRelationsSeeder::class, // Depende de Carreras (para info del usuario, si aplica)
            
            // Aquí podrías llamar al ProyectoSeeder si quisieras datos de proyectos de ejemplo.
            // ProyectoSeeder::class,
        ]);
    }
}