<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('institucion')->insert([
            [
                'nombre' => 'Universidad Central',
                'sigla' => 'UC',
                'descripcion' => 'Principal institución académica de la región.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Escuela Técnica Superior',
                'sigla' => 'ETS',
                'descripcion' => 'Especializada en desarrollo tecnológico.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}